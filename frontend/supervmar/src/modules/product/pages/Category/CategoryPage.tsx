import { useNavigate, useParams } from "react-router";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { type ChangeEvent, type FormEvent, useEffect, useState } from "react";
import { CategoryService } from "../../services/CategoryService.ts";
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

export const CategoryPage = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const { session } = useSession();
    const [name, setName] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [snackbarError, setSnackbarError] = useState<string | null>(null);

    const fetchCategory = async () => {
        try {
            if (!id) return;
            setIsLoading(true);
            const categories = await CategoryService.getCategories(session);
            const category = categories.find(c => c.id === id);
            if (category) {
                setName(category.name);
            } else {
                navigate('/productos/categorias');
            }
        } catch (error) {
            console.error('Error fetching category:', error);
            const message = error instanceof ApiError ? error.message : 'Error inesperado';
            setSnackbarError(message);
            navigate('/productos/categorias');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchCategory();
    }, [id, session]);

    const handleInputChange = (event: ChangeEvent<HTMLInputElement>) => {
        setName(event.target.value);
        if (error) {
            setError('');
        }
    };

    const validateForm = (): boolean => {
        if (!name.trim()) {
            setError('El nombre de la categoría es obligatorio');
            return false;
        }
        return true;
    };

    const handleSubmit = async (event: FormEvent) => {
        event.preventDefault();

        if (!validateForm() || !id) return;

        try {
            setIsSubmitting(true);
            await CategoryService.updateCategory(id, name.trim(), session);
            await fetchCategory();
        } catch (error) {
            console.error('Error updating category:', error);
            const message = error instanceof ApiError ? error.message : 'Error inesperado';
            setSnackbarError(message);
        } finally {
            setIsSubmitting(false);
        }
    };

    const deleteConfirmation = useDeleteConfirmation({
        onDelete: async () => {
            if (!id) return;
            await CategoryService.deleteCategory(id, session);
            navigate('/productos/categorias');
        },
        itemName: 'categoría'
    });

    if (isLoading) {
        return <LoadingComponent />;
    }

    return (
        <Box>
            <Typography variant="h4" gutterBottom sx={{ mb: 4 }}>
                Detalles de la categoría
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
                                onChange={handleInputChange}
                                error={!!error}
                                helperText={error}
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

