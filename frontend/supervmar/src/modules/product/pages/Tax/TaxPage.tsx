import { useNavigate, useParams } from "react-router";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { type ChangeEvent, type FormEvent, useEffect, useState } from "react";
import { TaxService } from "../../services/TaxService.ts";
import Paper from "@mui/material/Paper";
import { Grid, TextField, Typography } from "@mui/material";
import Box from "@mui/material/Box";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { DeleteButtonComponent } from "../../../commons/components/Buttons/DeleteButtonComponent.tsx";
import { ConfirmDialogComponent } from "../../../commons/components/ConfirmDialogComponent.tsx";
import { useDeleteConfirmation } from "../../../commons/hooks/useDeleteConfirmation.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import { ErrorSnackbarComponent } from "../../../commons/components/ErrorSnackbarComponent.tsx";
import { ApiError } from "../../../commons/services/HttpService.ts";

interface TaxFormErrors {
    name?: string;
    percent?: string;
}

export const TaxPage = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const { session } = useSession();
    const [name, setName] = useState('');
    const [percent, setPercent] = useState('');
    const [errors, setErrors] = useState<TaxFormErrors>({});
    const [isLoading, setIsLoading] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [snackbarError, setSnackbarError] = useState<string | null>(null);

    const fetchTax = async () => {
        try {
            if (!id) return;
            setIsLoading(true);
            const taxes = await TaxService.getTaxes(session);
            const tax = taxes.find(t => t.id === id);
            if (tax) {
                setName(tax.name);
                setPercent(String(tax.percent));
            } else {
                navigate('/productos/iva');
            }
        } catch (error) {
            console.error('Error fetching tax:', error);
            const message = error instanceof ApiError ? error.message : 'Error inesperado';
            setSnackbarError(message);
            navigate('/productos/iva');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchTax();
    }, [id, session]);

    const handleNameChange = (event: ChangeEvent<HTMLInputElement>) => {
        setName(event.target.value);
        if (errors.name) {
            setErrors(prev => ({ ...prev, name: undefined }));
        }
    };

    const handlePercentChange = (event: ChangeEvent<HTMLInputElement>) => {
        setPercent(event.target.value);
        if (errors.percent) {
            setErrors(prev => ({ ...prev, percent: undefined }));
        }
    };

    const validateForm = (): boolean => {
        const newErrors: TaxFormErrors = {};

        if (!name.trim()) {
            newErrors.name = 'El nombre del impuesto es obligatorio';
        }

        if (!percent.trim()) {
            newErrors.percent = 'El porcentaje es obligatorio';
        } else if (isNaN(Number(percent)) || Number(percent) < 0) {
            newErrors.percent = 'El porcentaje debe ser un número positivo';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (event: FormEvent) => {
        event.preventDefault();

        if (!validateForm() || !id) return;

        try {
            setIsSubmitting(true);
            await TaxService.updateTax(id, name.trim(), Number(percent), session);
            await fetchTax();
        } catch (error) {
            console.error('Error updating tax:', error);
            const message = error instanceof ApiError ? error.message : 'Error inesperado';
            setSnackbarError(message);
        } finally {
            setIsSubmitting(false);
        }
    };

    const deleteConfirmation = useDeleteConfirmation({
        onDelete: async () => {
            if (!id) return;
            await TaxService.deleteTax(id, session);
            navigate('/productos/iva');
        },
        itemName: 'impuesto'
    });

    if (isLoading) {
        return <LoadingComponent />;
    }

    return (
        <Box>
            <Typography variant="h4" gutterBottom sx={{ mb: 4 }}>
                Detalles del IVA
            </Typography>
            <Paper sx={{ maxWidth: 600, margin: 'auto', p: 3 }}>
                <Box component="form" onSubmit={handleSubmit} noValidate>
                    <Grid container spacing={3}>
                        <Grid size={{ xs: 12 }}>
                            <TextField
                                fullWidth
                                label="Nombre"
                                name="name"
                                value={name}
                                onChange={handleNameChange}
                                error={!!errors.name}
                                helperText={errors.name}
                                required
                            />
                        </Grid>
                        <Grid size={{ xs: 12 }}>
                            <TextField
                                fullWidth
                                label="Porcentaje"
                                name="percent"
                                type="number"
                                value={percent}
                                onChange={handlePercentChange}
                                error={!!errors.percent}
                                helperText={errors.percent}
                                required
                                slotProps={{
                                    input: {
                                        endAdornment: '%',
                                    }
                                }}
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
                message={deleteConfirmation.getDialogMessage(name)}
                confirmText="Eliminar"
                cancelText="Cancelar"
                confirmColor="error"
                onConfirm={deleteConfirmation.handleConfirm}
                onCancel={deleteConfirmation.closeDialog}
                isLoading={deleteConfirmation.isDeleting}
            />
            <ErrorSnackbarComponent
                open={!!snackbarError || !!deleteConfirmation.error}
                message={snackbarError || deleteConfirmation.error}
                onClose={() => {
                    setSnackbarError(null);
                    deleteConfirmation.clearError();
                }}
            />
        </Box>
    );
};

