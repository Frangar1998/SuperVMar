import { useNavigate } from "react-router";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { type ChangeEvent, type FormEvent, useState } from "react";
import { CategoryService } from "../../services/CategoryService.ts";
import Paper from "@mui/material/Paper";
import { Grid, TextField, Typography } from "@mui/material";
import Box from "@mui/material/Box";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { ErrorSnackbarComponent } from "../../../commons/components/ErrorSnackbarComponent.tsx";
import { ApiError } from "../../../commons/services/HttpService.ts";

export const CategoryCreatePage = () => {
    const navigate = useNavigate();
    const { session } = useSession();
    const [name, setName] = useState('');
    const [error, setError] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [snackbarError, setSnackbarError] = useState<string | null>(null);

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

        if (!validateForm()) return;

        try {
            setIsSubmitting(true);
            await CategoryService.createCategory(name.trim(), session);
            navigate('/productos/categorias');
        } catch (error) {
            console.error('Error creating category:', error);
            const message = error instanceof ApiError ? error.message : 'Error inesperado';
            setSnackbarError(message);
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <Box>
            <Typography variant="h4" gutterBottom sx={{ mb: 4 }}>
                Nueva categoría
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
                                autoFocus
                            />
                        </Grid>
                        <Grid size={{ xs: 12 }} sx={{ display: 'flex', justifyContent: 'flex-end', gap: 2 }}>
                            <ButtonComponent
                                text="Cancelar"
                                type="button"
                                variant="outlined"
                                color="primary"
                                size="medium"
                                onClick={() => navigate('/productos/categorias')}
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

