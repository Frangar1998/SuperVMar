import { useNavigate, useParams } from "react-router";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { type ChangeEvent, type FormEvent, useEffect, useState } from "react";
import { JobService } from "../../services/JobService.ts";
import Paper from "@mui/material/Paper";
import { Grid, TextField, Typography } from "@mui/material";
import Box from "@mui/material/Box";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { DeleteButtonComponent } from "../../../commons/components/Buttons/DeleteButtonComponent.tsx";
import { ConfirmDialogComponent } from "../../../commons/components/ConfirmDialogComponent.tsx";
import { useDeleteConfirmation } from "../../../commons/hooks/useDeleteConfirmation.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import { ErrorSnackbarComponent } from "../../../commons/components/ErrorSnackbarComponent.tsx";

export const JobPage = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const { session } = useSession();
    const [name, setName] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const fetchJob = async () => {
        try {
            if (!id) return;
            setIsLoading(true);
            const jobs = await JobService.getJobs(session);
            const job = jobs.find(j => j.id === id);
            if (job) {
                setName(job.name);
            } else {
                navigate('/usuarios/trabajos');
            }
        } catch (error) {
            console.error('Error fetching job:', error);
            navigate('/usuarios/trabajos');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchJob();
    }, [id, session]);

    const handleInputChange = (event: ChangeEvent<HTMLInputElement>) => {
        setName(event.target.value);
        if (error) {
            setError('');
        }
    };

    const validateForm = (): boolean => {
        if (!name.trim()) {
            setError('El nombre del trabajo es obligatorio');
            return false;
        }
        return true;
    };

    const handleSubmit = async (event: FormEvent) => {
        event.preventDefault();

        if (!validateForm() || !id) return;

        try {
            setIsSubmitting(true);
            await JobService.updateJob(id, name.trim(), session);
            navigate('/usuarios/trabajos');
        } catch (error) {
            console.error('Error updating job:', error);
        } finally {
            setIsSubmitting(false);
        }
    };

    const deleteConfirmation = useDeleteConfirmation({
        onDelete: async () => {
            if (!id) return;
            await JobService.deleteJob(id, session);
            navigate('/usuarios/trabajos');
        },
        itemName: 'trabajo'
    });

    if (isLoading) {
        return <LoadingComponent />;
    }

    return (
        <Box>
            <Typography variant="h4" gutterBottom sx={{ mb: 4 }}>
                Detalles del trabajo
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
                open={!!deleteConfirmation.error}
                message={deleteConfirmation.error}
                onClose={deleteConfirmation.clearError}
            />
        </Box>
    );
};
