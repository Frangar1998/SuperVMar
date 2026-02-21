import { useNavigate, useParams } from "react-router";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { type ChangeEvent, type FormEvent, useEffect, useState } from "react";
import { SupplierService } from "../../services/SupplierService.ts";
import type { SupplierFormData } from "../../types/ProductTypes.ts";
import Paper from "@mui/material/Paper";
import { Grid, TextField, Typography } from "@mui/material";
import Box from "@mui/material/Box";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { DeleteButtonComponent } from "../../../commons/components/Buttons/DeleteButtonComponent.tsx";
import { ConfirmDialogComponent } from "../../../commons/components/ConfirmDialogComponent.tsx";
import { useDeleteConfirmation } from "../../../commons/hooks/useDeleteConfirmation.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import { ErrorSnackbarComponent } from "../../../commons/components/ErrorSnackbarComponent.tsx";

const initialFormData: SupplierFormData = {
    name: "",
    phone: "",
    email: "",
    contact: "",
};

export const SupplierPage = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const { session } = useSession();
    const [formData, setFormData] = useState<SupplierFormData>(initialFormData);
    const [errors, setErrors] = useState<Partial<Record<keyof SupplierFormData, string>>>({});
    const [isLoading, setIsLoading] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const fetchSupplier = async () => {
        try {
            if (!id) return;
            setIsLoading(true);
            const suppliers = await SupplierService.getSuppliers(session);
            const supplier = suppliers.find(s => s.id === id);
            if (supplier) {
                setFormData({
                    name: supplier.name,
                    phone: supplier.phone,
                    email: supplier.email,
                    contact: supplier.contact,
                });
            } else {
                navigate('/productos/proveedores');
            }
        } catch (error) {
            console.error('Error fetching supplier:', error);
            navigate('/productos/proveedores');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchSupplier();
    }, [id, session]);

    const handleInputChange = (field: keyof SupplierFormData) => (
        event: ChangeEvent<HTMLInputElement>
    ) => {
        setFormData(prev => ({ ...prev, [field]: event.target.value }));
        if (errors[field]) {
            setErrors(prev => ({ ...prev, [field]: undefined }));
        }
    };

    const validateForm = (): boolean => {
        const newErrors: Partial<Record<keyof SupplierFormData, string>> = {};

        if (!formData.name.trim()) {
            newErrors.name = 'El nombre del proveedor es obligatorio';
        }
        if (!formData.phone.trim()) {
            newErrors.phone = 'El teléfono es obligatorio';
        }
        if (!formData.email.trim()) {
            newErrors.email = 'El email es obligatorio';
        }
        if (!formData.contact.trim()) {
            newErrors.contact = 'La persona de contacto es obligatoria';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (event: FormEvent) => {
        event.preventDefault();

        if (!validateForm() || !id) return;

        try {
            setIsSubmitting(true);
            await SupplierService.updateSupplier(id, {
                name: formData.name.trim(),
                phone: formData.phone.trim(),
                email: formData.email.trim(),
                contact: formData.contact.trim(),
            }, session);
            await fetchSupplier();
        } catch (error) {
            console.error('Error updating supplier:', error);
        } finally {
            setIsSubmitting(false);
        }
    };

    const deleteConfirmation = useDeleteConfirmation({
        onDelete: async () => {
            if (!id) return;
            await SupplierService.deleteSupplier(id, session);
            navigate('/productos/proveedores');
        },
        itemName: 'proveedor'
    });

    if (isLoading) {
        return <LoadingComponent />;
    }

    return (
        <Box>
            <Typography variant="h4" gutterBottom sx={{ mb: 4 }}>
                Detalles del proveedor
            </Typography>
            <Paper sx={{ maxWidth: 600, margin: 'auto', p: 3 }}>
                <Box component="form" onSubmit={handleSubmit} noValidate>
                    <Grid container spacing={3}>
                        <Grid size={{ xs: 12 }}>
                            <TextField
                                fullWidth
                                label="Nombre"
                                name="name"
                                value={formData.name}
                                onChange={handleInputChange('name')}
                                error={!!errors.name}
                                helperText={errors.name}
                                required
                            />
                        </Grid>
                        <Grid size={{ xs: 12, sm: 6 }}>
                            <TextField
                                fullWidth
                                label="Teléfono"
                                name="phone"
                                value={formData.phone}
                                onChange={handleInputChange('phone')}
                                error={!!errors.phone}
                                helperText={errors.phone}
                                required
                            />
                        </Grid>
                        <Grid size={{ xs: 12, sm: 6 }}>
                            <TextField
                                fullWidth
                                label="Email"
                                name="email"
                                type="email"
                                value={formData.email}
                                onChange={handleInputChange('email')}
                                error={!!errors.email}
                                helperText={errors.email}
                                required
                            />
                        </Grid>
                        <Grid size={{ xs: 12 }}>
                            <TextField
                                fullWidth
                                label="Persona de contacto"
                                name="contact"
                                value={formData.contact}
                                onChange={handleInputChange('contact')}
                                error={!!errors.contact}
                                helperText={errors.contact}
                                required
                            />
                        </Grid>
                        <Grid size={{ xs: 12 }} sx={{ display: 'flex', justifyContent: 'flex-end', gap: 2 }}>
                            <DeleteButtonComponent
                                text="Eliminar"
                                onClick={deleteConfirmation.openDialog}
                                disabled={isSubmitting || deleteConfirmation.isDeleting}
                            />
                            <ButtonComponent
                                text="Guardar"
                                type="submit"
                                variant="contained"
                                color="primary"
                                size="medium"
                                onClick={() => {}}
                                disabled={isSubmitting}
                            />
                        </Grid>
                    </Grid>
                </Box>
            </Paper>
            <ConfirmDialogComponent
                open={deleteConfirmation.isOpen}
                title={deleteConfirmation.getDialogTitle()}
                message={deleteConfirmation.getDialogMessage(formData.name)}
                confirmText="Eliminar"
                cancelText="Cancelar"
                confirmColor="error"
                onConfirm={deleteConfirmation.handleConfirm}
                onCancel={deleteConfirmation.closeDialog}
                isLoading={deleteConfirmation.isDeleting}
            />
            <ErrorSnackbarComponent
                open={!!deleteConfirmation.error}
                message={deleteConfirmation.error}
                onClose={deleteConfirmation.clearError}
            />
        </Box>
    );
};

