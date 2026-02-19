import { useNavigate } from "react-router";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { type ChangeEvent, type FormEvent, useState } from "react";
import { TaxService } from "../../services/TaxService.ts";
import Paper from "@mui/material/Paper";
import { Grid, TextField, Typography } from "@mui/material";
import Box from "@mui/material/Box";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";

interface TaxFormErrors {
    name?: string;
    percent?: string;
}

export const TaxCreatePage = () => {
    const navigate = useNavigate();
    const { session } = useSession();
    const [name, setName] = useState('');
    const [percent, setPercent] = useState('');
    const [errors, setErrors] = useState<TaxFormErrors>({});
    const [isSubmitting, setIsSubmitting] = useState(false);

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

        if (!validateForm()) return;

        try {
            setIsSubmitting(true);
            await TaxService.createTax(name.trim(), Number(percent), session);
            navigate('/productos/iva');
        } catch (error) {
            console.error('Error creating tax:', error);
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <Box>
            <Typography variant="h4" gutterBottom sx={{ mb: 4 }}>
                Nuevo IVA
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
                                autoFocus
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
                            <ButtonComponent
                                text="Cancelar"
                                type="button"
                                variant="outlined"
                                color="primary"
                                size="medium"
                                onClick={() => navigate('/productos/iva')}
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
        </Box>
    );
};

