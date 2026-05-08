import { useNavigate, useParams } from "react-router";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { type ChangeEvent, type FormEvent, useEffect, useState } from "react";
import { UserService } from "../../services/UserService.ts";
import { SupermarketService } from "../../../supermarket/services/SupermarketService.ts";
import { JobService } from "../../services/JobService.ts";
import type { UserFormData } from "../../types/UserTypes.ts";
import type { Supermarket } from "../../../supermarket/types/SupermarketTypes.ts";
import type { Job } from "../../types/UserTypes.ts";
import Paper from "@mui/material/Paper";
import { FormControl, FormControlLabel, Grid, IconButton, InputLabel, MenuItem, Select, Switch, TextField, Typography } from "@mui/material";
import Box from "@mui/material/Box";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { DeleteButtonComponent } from "../../../commons/components/Buttons/DeleteButtonComponent.tsx";
import { ConfirmDialogComponent } from "../../../commons/components/ConfirmDialogComponent.tsx";
import { useDeleteConfirmation } from "../../../commons/hooks/useDeleteConfirmation.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import { ErrorSnackbarComponent } from "../../../commons/components/ErrorSnackbarComponent.tsx";
import { ApiError } from "../../../commons/services/HttpService.ts";
import { translateApiError } from "../../utils/translateApiError.ts";
import { Add, Delete } from "@mui/icons-material";

const initialFormData: UserFormData = {
    username: "",
    password: "",
    passwordRepeat: "",
    isAdmin: false,
    name: "",
    surname: "",
    email: "",
    phone: "",
    addressName: "",
    postalCode: "",
    city: "",
    number: "",
    province: "",
    floor: "",
    door: "",
    other: "",
    allocations: [],
};

export const UserPage = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const { session } = useSession();
    const [formData, setFormData] = useState<UserFormData>(initialFormData);
    const [errors, setErrors] = useState<Partial<Record<string, string>>>({});
    const [isLoading, setIsLoading] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [apiError, setApiError] = useState<string | null>(null);
    const [userDataId, setUserDataId] = useState("");
    const [addressId, setAddressId] = useState("");
    const [supermarkets, setSupermarkets] = useState<Supermarket[]>([]);
    const [jobs, setJobs] = useState<Job[]>([]);


    const fetchData = async () => {
        try {
            if (!id) return;
            setIsLoading(true);
            const [user, supermarketsData, jobsData] = await Promise.all([
                UserService.getUser(id, session),
                SupermarketService.getSupermarkets(session),
                JobService.getJobs(session),
            ]);
            setSupermarkets(supermarketsData);
            setJobs(jobsData);
            setUserDataId(user.userData.id);
            setAddressId(user.userData.address.id);
            setFormData({
                username: user.username,
                password: "",
                passwordRepeat: "",
                isAdmin: user.isAdmin === 1,
                name: user.userData.name,
                surname: user.userData.surname,
                email: user.userData.email,
                phone: user.userData.phone,
                addressName: user.userData.address.name,
                postalCode: user.userData.address.postalCode,
                city: user.userData.address.city,
                number: user.userData.address.number,
                province: user.userData.address.province,
                floor: user.userData.address.floor ?? '',
                door: user.userData.address.door ?? '',
                other: user.userData.address.other ?? '',
                allocations: user.allocations.map(a => ({
                    idSupermarket: a.supermarket.id,
                    idJob: a.job.id,
                })),
            });
        } catch (error) {
            console.error('Error fetching user:', error);
            navigate('/usuarios/lista');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchData();
    }, [id, session]);

    const handleInputChange = (field: keyof UserFormData) => (
        event: ChangeEvent<HTMLInputElement>
    ) => {
        setFormData(prev => ({ ...prev, [field]: event.target.value }));
        if (errors[field]) {
            setErrors(prev => ({ ...prev, [field]: undefined }));
        }
    };

    const handleSwitchChange = (event: ChangeEvent<HTMLInputElement>) => {
        setFormData(prev => ({ ...prev, isAdmin: event.target.checked }));
    };

    const handleAllocationChange = (index: number, field: 'idSupermarket' | 'idJob', value: string) => {
        setFormData(prev => {
            const newAllocations = [...prev.allocations];
            newAllocations[index] = { ...newAllocations[index], [field]: value };
            return { ...prev, allocations: newAllocations };
        });
    };

    const handleAddAllocation = () => {
        setFormData(prev => ({
            ...prev,
            allocations: [...prev.allocations, { idSupermarket: "", idJob: "" }],
        }));
    };

    const handleRemoveAllocation = (index: number) => {
        setFormData(prev => ({
            ...prev,
            allocations: prev.allocations.filter((_, i) => i !== index),
        }));
    };

    const validateForm = (): boolean => {
        const newErrors: Partial<Record<string, string>> = {};

        if (!formData.username.trim()) newErrors.username = 'El usuario es obligatorio';
        if (!formData.name.trim()) newErrors.name = 'El nombre es obligatorio';
        if (!formData.surname.trim()) newErrors.surname = 'Los apellidos son obligatorios';
        if (!formData.email.trim()) newErrors.email = 'El email es obligatorio';
        if (!formData.phone.trim()) newErrors.phone = 'El teléfono es obligatorio';

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (event: FormEvent) => {
        event.preventDefault();

        if (!validateForm() || !id) return;

        try {
            setIsSubmitting(true);
            await UserService.updateUser(id, {
                username: formData.username.trim(),
                userData: {
                    id: userDataId,
                    name: formData.name.trim(),
                    surname: formData.surname.trim(),
                    email: formData.email.trim(),
                    phone: formData.phone.trim(),
                    address: {
                        id: addressId,
                        name: formData.addressName.trim(),
                        postalCode: formData.postalCode.trim(),
                        city: formData.city.trim(),
                        number: formData.number.trim(),
                        province: formData.province.trim(),
                        floor: formData.floor.trim(),
                        door: formData.door.trim(),
                        other: formData.other.trim(),
                    },
                },
                isAdmin: formData.isAdmin ? 1 : 0,
                allocations: formData.allocations,
            }, session);
            await fetchData();
        } catch (error) {
            if (error instanceof ApiError) {
                setApiError(translateApiError(error.message));
            } else {
                setApiError('Ha ocurrido un error inesperado.');
            }
        } finally {
            setIsSubmitting(false);
        }
    };

    const deleteConfirmation = useDeleteConfirmation({
        onDelete: async () => {
            if (!id) return;
            await UserService.deleteUser(id, session);
            navigate('/usuarios/lista');
        },
        itemName: 'usuario'
    });



    if (isLoading) {
        return <LoadingComponent />;
    }

    return (
        <Box>
            <Typography variant="h4" gutterBottom sx={{ mb: 4 }}>
                Detalles del usuario
            </Typography>
            <Paper sx={{ maxWidth: 800, margin: 'auto', p: 3 }}>
                <Box component="form" onSubmit={handleSubmit} noValidate>
                    <Grid container spacing={3}>
                        <Grid size={{ xs: 12 }}>
                            <Typography variant="h6">Cuenta</Typography>
                        </Grid>
                        <Grid size={{ xs: 12, sm: 6 }}>
                            <TextField
                                fullWidth
                                label="Usuario"
                                value={formData.username}
                                onChange={handleInputChange('username')}
                                error={!!errors.username}
                                helperText={errors.username}
                                required
                            />
                        </Grid>
                        <Grid size={{ xs: 12, sm: 6 }} sx={{ display: 'flex', alignItems: 'center' }}>
                            <FormControlLabel
                                control={
                                    <Switch
                                        checked={formData.isAdmin}
                                        onChange={handleSwitchChange}
                                    />
                                }
                                label="Administrador"
                            />
                        </Grid>

                        <Grid size={{ xs: 12 }}>
                            <Typography variant="h6">Datos personales</Typography>
                        </Grid>
                        <Grid size={{ xs: 12, sm: 6 }}>
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
                                label="Apellidos"
                                value={formData.surname}
                                onChange={handleInputChange('surname')}
                                error={!!errors.surname}
                                helperText={errors.surname}
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

                        <Grid size={{ xs: 12 }}>
                            <Typography variant="h6">Dirección</Typography>
                        </Grid>
                        <Grid size={{ xs: 12, sm: 6 }}>
                            <TextField
                                fullWidth
                                label="Calle"
                                value={formData.addressName}
                                onChange={handleInputChange('addressName')}
                            />
                        </Grid>
                        <Grid size={{ xs: 6, sm: 3 }}>
                            <TextField
                                fullWidth
                                label="Número"
                                value={formData.number}
                                onChange={handleInputChange('number')}
                            />
                        </Grid>
                        <Grid size={{ xs: 6, sm: 3 }}>
                            <TextField
                                fullWidth
                                label="Piso"
                                value={formData.floor}
                                onChange={handleInputChange('floor')}
                            />
                        </Grid>
                        <Grid size={{ xs: 6, sm: 3 }}>
                            <TextField
                                fullWidth
                                label="Puerta"
                                value={formData.door}
                                onChange={handleInputChange('door')}
                            />
                        </Grid>
                        <Grid size={{ xs: 6, sm: 3 }}>
                            <TextField
                                fullWidth
                                label="Código postal"
                                value={formData.postalCode}
                                onChange={handleInputChange('postalCode')}
                            />
                        </Grid>
                        <Grid size={{ xs: 12, sm: 6 }}>
                            <TextField
                                fullWidth
                                label="Ciudad"
                                value={formData.city}
                                onChange={handleInputChange('city')}
                            />
                        </Grid>
                        <Grid size={{ xs: 12, sm: 6 }}>
                            <TextField
                                fullWidth
                                label="Provincia"
                                value={formData.province}
                                onChange={handleInputChange('province')}
                            />
                        </Grid>
                        <Grid size={{ xs: 12 }}>
                            <TextField
                                fullWidth
                                label="Otros"
                                value={formData.other}
                                onChange={handleInputChange('other')}
                            />
                        </Grid>

                        <Grid size={{ xs: 12 }}>
                            <Typography variant="h6">Asignaciones</Typography>
                        </Grid>
                        <Grid size={{ xs: 12 }}>
                            {formData.allocations.map((allocation, index) => (
                                <Grid container spacing={2} key={index} sx={{ mb: 1 }}>
                                    <Grid size={{ xs: 5 }}>
                                        <FormControl fullWidth size="small">
                                            <InputLabel>Supermercado</InputLabel>
                                            <Select
                                                value={allocation.idSupermarket}
                                                label="Supermercado"
                                                onChange={(e) => handleAllocationChange(index, 'idSupermarket', e.target.value as string)}
                                            >
                                                {supermarkets.map((s) => (
                                                    <MenuItem key={s.id} value={s.id}>{s.name}</MenuItem>
                                                ))}
                                            </Select>
                                        </FormControl>
                                    </Grid>
                                    <Grid size={{ xs: 5 }}>
                                        <FormControl fullWidth size="small">
                                            <InputLabel>Trabajo</InputLabel>
                                            <Select
                                                value={allocation.idJob}
                                                label="Trabajo"
                                                onChange={(e) => handleAllocationChange(index, 'idJob', e.target.value as string)}
                                            >
                                                {jobs.map((j) => (
                                                    <MenuItem key={j.id} value={j.id}>{j.name}</MenuItem>
                                                ))}
                                            </Select>
                                        </FormControl>
                                    </Grid>
                                    <Grid size={{ xs: 2 }} sx={{ display: 'flex', alignItems: 'center' }}>
                                        <IconButton onClick={() => handleRemoveAllocation(index)} color="error" size="small">
                                            <Delete />
                                        </IconButton>
                                    </Grid>
                                </Grid>
                            ))}
                            <ButtonComponent
                                text="Añadir asignación"
                                type="button"
                                variant="outlined"
                                color="primary"
                                size="small"
                                startIcon={<Add />}
                                onClick={handleAddAllocation}
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
                message={deleteConfirmation.getDialogMessage(formData.username)}
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
            <ErrorSnackbarComponent
                open={!!apiError}
                message={apiError}
                onClose={() => setApiError(null)}
                autoHideDuration={10000}
            />
        </Box>
    );
};
