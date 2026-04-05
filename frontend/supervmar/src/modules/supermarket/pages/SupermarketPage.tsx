import { useSession } from "../../login/contexts/SessionContext.ts";
import { type ChangeEvent, type FormEvent, useEffect, useState } from "react";
import { SupermarketService } from "../services/SupermarketService.ts";
import type { SupermarketFormData, ZoneFormData } from "../types/SupermarketTypes.ts";
import Paper from "@mui/material/Paper";
import { Grid, TextField, Typography } from "@mui/material";
import Box from "@mui/material/Box";
import { ButtonComponent } from "../../commons/components/Buttons/ButtonComponent.tsx";
import { LoadingComponent } from "../../commons/components/LoadingComponent.tsx";
import { SupermarketMapComponent } from "../components/Map/SupermarketMapComponent.tsx";
import { ErrorSnackbarComponent } from "../../commons/components/ErrorSnackbarComponent.tsx";
import { ApiError } from "../../commons/services/HttpService.ts";

const initialFormData: SupermarketFormData = {
    name: "",
    phone: "",
    email: "",
    address: {
        id: "",
        name: "",
        number: "",
        postalCode: "",
        city: "",
        province: "",
    },
    zones: [],
};

export const SupermarketPage = () => {
    const { session } = useSession();
    const [supermarketId, setSupermarketId] = useState<string | null>(null);
    const [formData, setFormData] = useState<SupermarketFormData>(initialFormData);
    const [zones, setZones] = useState<ZoneFormData[]>([]);
    const [errors, setErrors] = useState<Partial<Record<string, string>>>({});
    const [isLoading, setIsLoading] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [snackbarError, setSnackbarError] = useState<string | null>(null);

    const fetchSupermarket = async () => {
        try {
            setIsLoading(true);
            const supermarkets = await SupermarketService.getSupermarkets(session);
            if (supermarkets.length > 0) {
                const s = supermarkets[0];
                setSupermarketId(s.id);
                setFormData({
                    name: s.name,
                    phone: s.phone,
                    email: s.email,
                    address: {
                        id: s.address.id,
                        name: s.address.name,
                        number: s.address.number,
                        postalCode: s.address.postalCode,
                        city: s.address.city,
                        province: s.address.province,
                    },
                    zones: [],
                });
                if (s.zones) {
                    setZones(s.zones.map(z => ({
                        id: z.id,
                        name: z.name,
                        cornerTopLeft: z.cornerTopLeft,
                        cornerTopRight: z.cornerTopRight,
                        cornerBottomRight: z.cornerBottomRight,
                        cornerBottomLeft: z.cornerBottomLeft,
                        spaces: z.spaces?.map(sp => ({
                            id: sp.id,
                            position: sp.position,
                            maxSpots: sp.maxSpots,
                        })) ?? [],
                    })));
                }
            }
        } catch (error) {
            console.error('Error fetching supermarket:', error);
            const message = error instanceof ApiError ? error.message : 'Error inesperado';
            setSnackbarError(message);
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchSupermarket();
    }, []);

    const handleInputChange = (field: string) => (event: ChangeEvent<HTMLInputElement>) => {
        const value = event.target.value;

        if (field.startsWith('address.')) {
            const addressField = field.split('.')[1];
            setFormData(prev => ({
                ...prev,
                address: { ...prev.address, [addressField]: value },
            }));
        } else {
            setFormData(prev => ({ ...prev, [field]: value }));
        }

        if (errors[field]) {
            setErrors(prev => ({ ...prev, [field]: undefined }));
        }
    };

    const validateForm = (): boolean => {
        const newErrors: Partial<Record<string, string>> = {};

        if (!formData.name.trim()) newErrors.name = 'El nombre es obligatorio';
        if (!formData.phone.trim()) newErrors.phone = 'El teléfono es obligatorio';
        if (!formData.email.trim()) newErrors.email = 'El email es obligatorio';
        if (!formData.address.name.trim()) newErrors['address.name'] = 'El nombre de la dirección es obligatorio';
        if (!formData.address.number.trim()) newErrors['address.number'] = 'El número es obligatorio';
        if (!formData.address.city.trim()) newErrors['address.city'] = 'La ciudad es obligatoria';
        if (!formData.address.postalCode.trim()) newErrors['address.postalCode'] = 'El código postal es obligatorio';
        if (!formData.address.province.trim()) newErrors['address.province'] = 'La provincia es obligatoria';

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (event?: FormEvent) => {
        if (event) event.preventDefault();
        if (!validateForm()) return;

        try {
            setIsSubmitting(true);
            const payload: SupermarketFormData = {
                name: formData.name.trim(),
                phone: formData.phone.trim(),
                email: formData.email.trim(),
                address: {
                    id: formData.address.id,
                    name: formData.address.name.trim(),
                    number: formData.address.number.trim(),
                    postalCode: formData.address.postalCode.trim(),
                    city: formData.address.city.trim(),
                    province: formData.address.province.trim(),
                },
                zones: zones.map(z => ({
                    id: z.id,
                    name: z.name,
                    cornerTopLeft: z.cornerTopLeft,
                    cornerTopRight: z.cornerTopRight,
                    cornerBottomRight: z.cornerBottomRight,
                    cornerBottomLeft: z.cornerBottomLeft,
                    spaces: z.spaces.map(sp => ({
                        id: sp.id,
                        position: sp.position,
                        maxSpots: sp.maxSpots,
                    })),
                })),
            };

            if (supermarketId) {
                await SupermarketService.updateSupermarket(supermarketId, payload, session);
            } else {
                await SupermarketService.createSupermarket(payload, session);
            }
            await fetchSupermarket();
        } catch (error) {
            console.error('Error saving supermarket:', error);
            const message = error instanceof ApiError ? error.message : 'Error inesperado';
            setSnackbarError(message);
        } finally {
            setIsSubmitting(false);
        }
    };

    if (isLoading) {
        return <LoadingComponent />;
    }

    return (
        <Box>
            <Typography variant="h4" gutterBottom sx={{ mb: 4 }}>
                {supermarketId ? 'Datos del supermercado' : 'Crear supermercado'}
            </Typography>
            <Paper sx={{ maxWidth: 600, margin: 'auto', p: 3 }}>
                <Box component="form" onSubmit={handleSubmit} noValidate>
                    <Grid container spacing={3}>
                        <Grid size={{ xs: 12 }}>
                            <TextField
                                fullWidth
                                label="Nombre"
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
                                type="email"
                                value={formData.email}
                                onChange={handleInputChange('email')}
                                error={!!errors.email}
                                helperText={errors.email}
                                required
                            />
                        </Grid>

                        <Grid size={{ xs: 12 }}>
                            <Typography variant="h6" sx={{ mt: 1 }}>
                                Dirección
                            </Typography>
                        </Grid>
                        <Grid size={{ xs: 12, sm: 8 }}>
                            <TextField
                                fullWidth
                                label="Calle"
                                value={formData.address.name}
                                onChange={handleInputChange('address.name')}
                                error={!!errors['address.name']}
                                helperText={errors['address.name']}
                                required
                            />
                        </Grid>
                        <Grid size={{ xs: 12, sm: 4 }}>
                            <TextField
                                fullWidth
                                label="Número"
                                value={formData.address.number}
                                onChange={handleInputChange('address.number')}
                                error={!!errors['address.number']}
                                helperText={errors['address.number']}
                                required
                            />
                        </Grid>
                        <Grid size={{ xs: 12, sm: 4 }}>
                            <TextField
                                fullWidth
                                label="Ciudad"
                                value={formData.address.city}
                                onChange={handleInputChange('address.city')}
                                error={!!errors['address.city']}
                                helperText={errors['address.city']}
                                required
                            />
                        </Grid>
                        <Grid size={{ xs: 12, sm: 4 }}>
                            <TextField
                                fullWidth
                                label="Código postal"
                                value={formData.address.postalCode}
                                onChange={handleInputChange('address.postalCode')}
                                error={!!errors['address.postalCode']}
                                helperText={errors['address.postalCode']}
                                required
                            />
                        </Grid>
                        <Grid size={{ xs: 12, sm: 4 }}>
                            <TextField
                                fullWidth
                                label="Provincia"
                                value={formData.address.province}
                                onChange={handleInputChange('address.province')}
                                error={!!errors['address.province']}
                                helperText={errors['address.province']}
                                required
                            />
                        </Grid>
                    </Grid>
                </Box>
            </Paper>

            {supermarketId && (
                <Box sx={{ mt: 5 }}>
                    <Typography variant="h4" gutterBottom sx={{ mb: 3 }}>
                        Mapa del supermercado
                    </Typography>
                    <SupermarketMapComponent
                        zones={zones}
                        onZonesChange={setZones}
                    />
                </Box>
            )}

            <Paper
                elevation={3}
                sx={{
                    position: 'fixed',
                    bottom: 0,
                    left: 0,
                    right: 0,
                    p: 2,
                    display: 'flex',
                    justifyContent: 'flex-end',
                    zIndex: 1200,
                    borderRadius: 0,
                }}
            >
                <ButtonComponent
                    text={supermarketId ? 'Guardar' : 'Crear'}
                    type="button"
                    variant="contained"
                    color="primary"
                    size="medium"
                    onClick={handleSubmit}
                    disabled={isSubmitting}
                />
            </Paper>

            <Box sx={{ height: 80 }} />
            <ErrorSnackbarComponent
                open={!!snackbarError}
                message={snackbarError}
                onClose={() => setSnackbarError(null)}
            />
        </Box>
    );
};