import { useState, useEffect } from "react";
import {
    Dialog, DialogActions, DialogContent, DialogTitle,
    Grid, TextField, Typography, MenuItem, Alert
} from "@mui/material";
import { ButtonComponent } from "../../commons/components/Buttons/ButtonComponent.tsx";
import { SHELF_DIRECTIONS, directionLabel } from "../types/SupermarketTypes.ts";
import type { SpaceFormData } from "../types/SupermarketTypes.ts";

interface SpaceBulkDialogProps {
    open: boolean;
    existingSpaces: SpaceFormData[];
    onConfirm: (spaces: { x: number; y: number; z: number; maxSpots: number }[]) => void;
    onCancel: () => void;
}

export const SpaceBulkDialogComponent = ({ open, existingSpaces, onConfirm, onCancel }: SpaceBulkDialogProps) => {
    const [direction, setDirection] = useState<number>(0);
    const [levels, setLevels] = useState('3');
    const [columns, setColumns] = useState('4');
    const [maxSpots, setMaxSpots] = useState('1');
    const [errors, setErrors] = useState<Record<string, string>>({});

    useEffect(() => {
        if (open) {
            setDirection(0);
            setLevels('3');
            setColumns('4');
            setMaxSpots('1');
            setErrors({});
        }
    }, [open]);

    const existingInDirection = existingSpaces.filter(s => s.position.y === direction);

    const levelsNum = parseInt(levels) || 0;
    const columnsNum = parseInt(columns) || 0;
    const total = levelsNum * columnsNum;

    const existingPositions = new Set(
        existingInDirection.map(s => `${s.position.x},${s.position.z}`)
    );

    let wouldSkip = 0;
    for (let x = 0; x < columnsNum; x++) {
        for (let z = 0; z < levelsNum; z++) {
            if (existingPositions.has(`${x},${z}`)) wouldSkip++;
        }
    }
    const wouldCreate = total - wouldSkip;

    const validate = (): boolean => {
        const newErrors: Record<string, string> = {};
        const l = parseInt(levels);
        const c = parseInt(columns);
        const m = parseInt(maxSpots);

        if (isNaN(l) || l < 1) {
            newErrors.levels = 'Debe ser al menos 1';
        } else if (l > 20) {
            newErrors.levels = 'Máximo 20 niveles';
        }

        if (isNaN(c) || c < 1) {
            newErrors.columns = 'Debe ser al menos 1';
        } else if (c > 50) {
            newErrors.columns = 'Máximo 50 huecos';
        }

        if (isNaN(m) || m < 1) {
            newErrors.maxSpots = 'Debe ser al menos 1';
        }

        if (Object.keys(newErrors).length === 0 && wouldCreate === 0) {
            newErrors.columns = 'Todas las posiciones ya están ocupadas';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleConfirm = () => {
        if (!validate()) return;

        const l = parseInt(levels);
        const c = parseInt(columns);
        const m = parseInt(maxSpots);

        const newSpaces: { x: number; y: number; z: number; maxSpots: number }[] = [];

        for (let x = 0; x < c; x++) {
            for (let z = 0; z < l; z++) {
                if (!existingPositions.has(`${x},${z}`)) {
                    newSpaces.push({ x, y: direction, z, maxSpots: m });
                }
            }
        }

        onConfirm(newSpaces);
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleConfirm();
        }
    };

    return (
        <Dialog open={open} onClose={onCancel} maxWidth="xs" fullWidth>
            <DialogTitle>Añadir espacios en bloque</DialogTitle>
            <DialogContent>
                <Typography variant="body2" color="text.secondary" sx={{ mb: 2 }}>
                    Genera automáticamente todos los espacios de una estantería completa.
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
                                    {existingSpaces.filter(s => s.position.y === d.y).length > 0 &&
                                        ` (${existingSpaces.filter(s => s.position.y === d.y).length} espacios existentes)`
                                    }
                                </MenuItem>
                            ))}
                        </TextField>
                    </Grid>

                    <Grid size={{ xs: 6 }}>
                        <TextField
                            fullWidth
                            label="Niveles de altura"
                            type="number"
                            value={levels}
                            onChange={(e) => { setLevels(e.target.value); setErrors(prev => ({ ...prev, levels: '' })); }}
                            onKeyDown={handleKeyDown}
                            error={!!errors.levels}
                            helperText={errors.levels || `Z: 0 a ${(parseInt(levels) || 1) - 1}`}
                            slotProps={{ htmlInput: { min: 1, max: 20 } }}
                        />
                    </Grid>

                    <Grid size={{ xs: 6 }}>
                        <TextField
                            fullWidth
                            label="Huecos por nivel"
                            type="number"
                            value={columns}
                            onChange={(e) => { setColumns(e.target.value); setErrors(prev => ({ ...prev, columns: '' })); }}
                            onKeyDown={handleKeyDown}
                            error={!!errors.columns}
                            helperText={errors.columns || `X: 0 a ${(parseInt(columns) || 1) - 1}`}
                            slotProps={{ htmlInput: { min: 1, max: 50 } }}
                        />
                    </Grid>

                    <Grid size={{ xs: 12 }}>
                        <TextField
                            fullWidth
                            label="Capacidad máxima por espacio"
                            type="number"
                            value={maxSpots}
                            onChange={(e) => { setMaxSpots(e.target.value); setErrors(prev => ({ ...prev, maxSpots: '' })); }}
                            onKeyDown={handleKeyDown}
                            error={!!errors.maxSpots}
                            helperText={errors.maxSpots || 'Productos que caben en cada hueco'}
                            slotProps={{ htmlInput: { min: 1 } }}
                        />
                    </Grid>
                </Grid>

                {total > 0 && (
                    <Alert severity={wouldSkip > 0 ? "warning" : "info"} sx={{ mt: 2 }}>
                        <Typography variant="body2">
                            Se crearán <strong>{wouldCreate}</strong> espacios
                            en el estante <strong>{directionLabel(direction)}</strong>
                            {" "}({parseInt(columns) || 0} columnas × {parseInt(levels) || 0} niveles)
                        </Typography>
                        {wouldSkip > 0 && (
                            <Typography variant="body2" sx={{ mt: 0.5 }}>
                                Se omitirán <strong>{wouldSkip}</strong> posiciones ya ocupadas.
                            </Typography>
                        )}
                    </Alert>
                )}
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
                    text={`Crear ${wouldCreate} espacios`}
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




