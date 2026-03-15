import { useState, useEffect } from "react";
import {
    Dialog, DialogActions, DialogContent, DialogTitle,
    Grid, TextField, Typography, MenuItem
} from "@mui/material";
import { ButtonComponent } from "../../commons/components/Buttons/ButtonComponent.tsx";
import { SHELF_DIRECTIONS } from "../types/SupermarketTypes.ts";
import type { SpaceFormData } from "../types/SupermarketTypes.ts";

interface SpaceDialogProps {
    open: boolean;
    zoneWidth: number;
    zoneHeight: number;
    existingSpaces: SpaceFormData[];
    onConfirm: (data: { x: number; y: number; z: number; maxSpots: number }) => void;
    onCancel: () => void;
}

export const SpaceDialogComponent = ({ open, zoneWidth, zoneHeight, existingSpaces, onConfirm, onCancel }: SpaceDialogProps) => {
    const [direction, setDirection] = useState<number>(0);
    const [x, setX] = useState('0');
    const [z, setZ] = useState('0');
    const [maxSpots, setMaxSpots] = useState('1');
    const [errors, setErrors] = useState<Record<string, string>>({});

    useEffect(() => {
        if (open) {
            setDirection(0);
            setX('0');
            setZ('0');
            setMaxSpots('1');
            setErrors({});
        }
    }, [open]);

    const maxX = (direction === 0 || direction === 1) ? zoneWidth : zoneHeight;

    const validate = (): boolean => {
        const newErrors: Record<string, string> = {};
        const xNum = parseInt(x);
        const zNum = parseInt(z);
        const maxNum = parseInt(maxSpots);

        if (isNaN(xNum) || xNum < 0) {
            newErrors.x = 'Debe ser un número ≥ 0';
        }

        if (isNaN(zNum) || zNum < 0) {
            newErrors.z = 'Debe ser un número ≥ 0';
        }

        if (isNaN(maxNum) || maxNum < 1) {
            newErrors.maxSpots = 'Debe ser al menos 1';
        }

        if (!isNaN(xNum) && !isNaN(zNum)) {
            const duplicate = existingSpaces.find(
                s => s.position.x === xNum && s.position.y === direction && s.position.z === zNum
            );
            if (duplicate) {
                newErrors.x = 'Ya existe un espacio en esta posición';
                newErrors.z = 'Ya existe un espacio en esta posición';
            }
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleConfirm = () => {
        if (!validate()) return;
        onConfirm({
            x: parseInt(x),
            y: direction,
            z: parseInt(z),
            maxSpots: parseInt(maxSpots),
        });
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleConfirm();
        }
    };

    return (
        <Dialog open={open} onClose={onCancel} maxWidth="xs" fullWidth>
            <DialogTitle>Añadir espacio</DialogTitle>
            <DialogContent>
                <Typography variant="body2" color="text.secondary" sx={{ mb: 2 }}>
                    Selecciona el estante (dirección), la posición horizontal y el nivel de altura.
                </Typography>
                <Grid container spacing={2}>
                    <Grid size={{ xs: 12 }}>
                        <TextField
                            select
                            fullWidth
                            label="Estante (dirección)"
                            value={direction}
                            onChange={(e) => { setDirection(Number(e.target.value)); setErrors({}); }}
                        >
                            {SHELF_DIRECTIONS.map((d) => (
                                <MenuItem key={d.y} value={d.y}>
                                    {d.label}
                                </MenuItem>
                            ))}
                        </TextField>
                    </Grid>
                    <Grid size={{ xs: 6 }}>
                        <TextField
                            fullWidth
                            label="Posición (X)"
                            type="number"
                            value={x}
                            onChange={(e) => { setX(e.target.value); setErrors(prev => ({ ...prev, x: '' })); }}
                            onKeyDown={handleKeyDown}
                            error={!!errors.x}
                            helperText={errors.x || `Horizontal (0–${maxX - 1})`}
                            slotProps={{ htmlInput: { min: 0 } }}
                        />
                    </Grid>
                    <Grid size={{ xs: 6 }}>
                        <TextField
                            fullWidth
                            label="Nivel (Z)"
                            type="number"
                            value={z}
                            onChange={(e) => { setZ(e.target.value); setErrors(prev => ({ ...prev, z: '' })); }}
                            onKeyDown={handleKeyDown}
                            error={!!errors.z}
                            helperText={errors.z || '0 = suelo'}
                            slotProps={{ htmlInput: { min: 0 } }}
                        />
                    </Grid>
                    <Grid size={{ xs: 12 }}>
                        <TextField
                            fullWidth
                            label="Capacidad máxima"
                            type="number"
                            value={maxSpots}
                            onChange={(e) => { setMaxSpots(e.target.value); setErrors(prev => ({ ...prev, maxSpots: '' })); }}
                            onKeyDown={handleKeyDown}
                            error={!!errors.maxSpots}
                            helperText={errors.maxSpots || 'Número de productos que caben'}
                            slotProps={{ htmlInput: { min: 1 } }}
                        />
                    </Grid>
                </Grid>
            </DialogContent>
            <DialogActions>
                <ButtonComponent
                    text="Cancelar"
                    type="button"
                    variant="text"
                    color="primary"
                    size="medium"
                    onClick={onCancel}
                />
                <ButtonComponent
                    text="Añadir"
                    type="button"
                    variant="contained"
                    color="primary"
                    size="medium"
                    onClick={handleConfirm}
                />
            </DialogActions>
        </Dialog>
    );
};

