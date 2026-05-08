import { useNavigate } from "react-router";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { type ChangeEvent, type FormEvent, useState } from "react";
import { SupplierService } from "../../services/SupplierService.ts";
import type { SupplierFormData } from "../../types/ProductTypes.ts";
import Paper from "@mui/material/Paper";
import { Grid, TextField, Typography } from "@mui/material";
import Box from "@mui/material/Box";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { ErrorSnackbarComponent } from "../../../commons/components/ErrorSnackbarComponent.tsx";
import { ApiError } from "../../../commons/services/HttpService.ts";

const initialFormData: SupplierFormData = {
    name: "",
    phone: "",
    email: "",
    contact: "",
};

export const SupplierCreatePage = () => {
    const navigate = useNavigate();
    const { session } = useSession();
    const [formData, setFormData] = useState<SupplierFormData>(initialFormData);
    const [errors, setErrors] = useState<Partial<Record<keyof SupplierFormData, string>>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [snackbarError, setSnackbarError] = useState<string | null>(null);

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

        if (!validateForm()) return;

        try {
            setIsSubmitting(true);
            await SupplierService.createSupplier({
                name: formData.name.trim(),
                phone: formData.phone.trim(),
                email: formData.email.trim(),
                contact: formData.contact.trim(),
            }, session);
            navigate('/productos/proveedores');
        } catch (error) {
            console.error('Error creating supplier:', error);
            const message = error instanceof ApiError ? error.message : 'Error inesperado';
            setSnackbarError(message);
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <Box>
            <Typography variant="h4" gutterBottom sx={{ mb: 4 }}>
                Nuevo proveedor
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
                                autoFocus
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
                            <ButtonComponent
                                text="Cancelar"
                                type="button"
                                variant="outlined"
                                color="primary"
                                size="medium"
                                onClick={() => navigate('/productos/proveedores')}
                                disabled={isSubmitting}
                            />
                            <ButtonComponent
                                text="Crear"
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
            <ErrorSnackbarComponent
                open={!!snackbarError}
                message={snackbarError}
                onClose={() => setSnackbarError(null)}
            />
        </Box>
    );
};

