import { useState } from "react";
import { Typography, IconButton, Tooltip } from "@mui/material";
import { Add, Close, ArrowBack, ArrowForward, Delete, ViewModule } from "@mui/icons-material";
import Box from "@mui/material/Box";
import Paper from "@mui/material/Paper";
import type { ZoneFormData, SpaceFormData } from "../types/SupermarketTypes.ts";
import { directionLabel, SHELF_DIRECTIONS } from "../types/SupermarketTypes.ts";
import { ButtonComponent } from "../../commons/components/Buttons/ButtonComponent.tsx";
import { SpaceDialogComponent } from "./SpaceDialogComponent.tsx";
import { SpaceBulkDialogComponent } from "./SpaceBulkDialogComponent.tsx";
import { ConfirmDialogComponent } from "../../commons/components/ConfirmDialogComponent.tsx";
import { v7 } from "uuid";

interface SpacesPanelProps {
    zone: ZoneFormData;
    onSpacesChange: (spaces: SpaceFormData[]) => void;
    onClose: () => void;
}

const CELL_W = 90;
const CELL_H = 52;
const GAP = 6;
const WRAPPER_PAD = 16;
const BORDER_COLOR = '#e67e22';

const getActiveDirections = (spaces: SpaceFormData[]): number[] => {
    const dirs = new Set(spaces.map(s => s.position.y));
    return SHELF_DIRECTIONS.filter(d => dirs.has(d.y)).map(d => d.y);
};

const buildGrid = (spaces: SpaceFormData[], dirY: number) => {
    const filtered = spaces.filter(s => s.position.y === dirY);
    const map = new Map<string, SpaceFormData>();
    for (const s of filtered) {
        map.set(`${s.position.x},${s.position.z}`, s);
    }
    const maxX = filtered.reduce((m, s) => Math.max(m, s.position.x), 0);
    const maxZ = filtered.reduce((m, s) => Math.max(m, s.position.z), 0);

    const numberMap = new Map<string, number>();
    let seq = 1;
    for (let z = maxZ; z >= 0; z--) {
        for (let x = 0; x <= maxX; x++) {
            if (map.has(`${x},${z}`)) {
                numberMap.set(`${x},${z}`, seq++);
            }
        }
    }

    return { map, numberMap, maxX, maxZ, count: filtered.length };
};

export const SpacesPanelComponent = ({ zone, onSpacesChange, onClose }: SpacesPanelProps) => {
    const [currentDirIdx, setCurrentDirIdx] = useState(0);
    const [addDialogOpen, setAddDialogOpen] = useState(false);
    const [bulkDialogOpen, setBulkDialogOpen] = useState(false);
    const [deleteSpaceId, setDeleteSpaceId] = useState<string | null>(null);

    const activeDirections = getActiveDirections(zone.spaces);
    const currentDirY = activeDirections[currentDirIdx] ?? null;

    const zoneWidth = zone.cornerTopRight.x - zone.cornerTopLeft.x;
    const zoneHeight = zone.cornerTopLeft.y - zone.cornerBottomLeft.y;

    const safeIdx = activeDirections.length === 0 ? 0 : Math.min(currentDirIdx, activeDirections.length - 1);
    if (safeIdx !== currentDirIdx && activeDirections.length > 0) {
        setCurrentDirIdx(safeIdx);
    }

    const handlePrev = () => setCurrentDirIdx(i => Math.max(0, i - 1));
    const handleNext = () => setCurrentDirIdx(i => Math.min(activeDirections.length - 1, i + 1));

    const handleAddSpace = (data: { x: number; y: number; z: number; maxSpots: number }) => {
        const newSpace: SpaceFormData = {
            id: v7(),
            position: { x: data.x, y: data.y, z: data.z },
            maxSpots: data.maxSpots,
        };
        const updatedSpaces = [...zone.spaces, newSpace];
        onSpacesChange(updatedSpaces);
        setAddDialogOpen(false);

        const updatedDirs = getActiveDirections(updatedSpaces);
        const idx = updatedDirs.indexOf(data.y);
        if (idx >= 0) setCurrentDirIdx(idx);
    };

    const handleBulkAdd = (spaces: { x: number; y: number; z: number; maxSpots: number }[]) => {
        const newSpaces: SpaceFormData[] = spaces.map(s => ({
            id: v7(),
            position: { x: s.x, y: s.y, z: s.z },
            maxSpots: s.maxSpots,
        }));
        const updatedSpaces = [...zone.spaces, ...newSpaces];
        onSpacesChange(updatedSpaces);
        setBulkDialogOpen(false);

        if (newSpaces.length > 0) {
            const dir = newSpaces[0].position.y;
            const updatedDirs = getActiveDirections(updatedSpaces);
            const idx = updatedDirs.indexOf(dir);
            if (idx >= 0) setCurrentDirIdx(idx);
        }
    };

    const handleDeleteSpace = () => {
        if (!deleteSpaceId) return;
        const updated = zone.spaces.filter(s => s.id !== deleteSpaceId);
        onSpacesChange(updated);
        setDeleteSpaceId(null);

        const newDirs = getActiveDirections(updated);
        if (currentDirIdx >= newDirs.length) {
            setCurrentDirIdx(Math.max(0, newDirs.length - 1));
        }
    };

    const deleteSpace = zone.spaces.find(s => s.id === deleteSpaceId);

    const grid = currentDirY !== null ? buildGrid(zone.spaces, currentDirY) : null;

    const cols = grid ? grid.maxX + 1 : 0;
    const rows = grid ? grid.maxZ + 1 : 0;
    const wrapperW = cols * (CELL_W + GAP) - GAP + WRAPPER_PAD * 2;
    const wrapperH = rows * (CELL_H + GAP) - GAP + WRAPPER_PAD * 2;

    return (
        <Paper variant="outlined" sx={{ mt: 2, p: 3 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                <Typography variant="h6">
                    Espacios de {zone.name}
                </Typography>
                <Box sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
                    <ButtonComponent
                        text="Añadir en bloque"
                        type="button"
                        variant="contained"
                        color="secondary"
                        size="small"
                        disableElevation
                        startIcon={<ViewModule />}
                        onClick={() => setBulkDialogOpen(true)}
                    />
                    <ButtonComponent
                        text="Añadir espacio"
                        type="button"
                        variant="outlined"
                        color="primary"
                        size="small"
                        disableElevation
                        startIcon={<Add />}
                        onClick={() => setAddDialogOpen(true)}
                    />
                    <Tooltip title="Cerrar panel">
                        <IconButton onClick={onClose} size="small">
                            <Close />
                        </IconButton>
                    </Tooltip>
                </Box>
            </Box>

            {activeDirections.length === 0 ? (
                <Box sx={{ textAlign: 'center', py: 4, color: 'text.secondary' }}>
                    <Typography>No hay espacios definidos en esta zona.</Typography>
                    <Typography variant="body2" sx={{ mt: 1 }}>
                        Pulsa "Añadir espacio" para crear el primer estante.
                    </Typography>
                </Box>
            ) : (
                <>
                    <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', gap: 2, mb: 2 }}>
                        <IconButton onClick={handlePrev} disabled={currentDirIdx === 0} size="small">
                            <ArrowBack />
                        </IconButton>
                        <Typography variant="subtitle1" sx={{ fontWeight: 'bold', minWidth: 220, textAlign: 'center' }}>
                            Estante {directionLabel(currentDirY!)} — {currentDirIdx + 1} / {activeDirections.length}
                        </Typography>
                        <IconButton onClick={handleNext} disabled={currentDirIdx === activeDirections.length - 1} size="small">
                            <ArrowForward />
                        </IconButton>
                    </Box>

                    <Typography variant="caption" color="text.secondary" sx={{ display: 'block', textAlign: 'center', mb: 1 }}>
                        Vista frontal del estante
                    </Typography>

                    <Box sx={{ display: 'flex', justifyContent: 'center' }}>
                        <Box
                            sx={{
                                display: 'inline-block',
                                border: `3px solid ${BORDER_COLOR}`,
                                borderRadius: 2,
                                p: `${WRAPPER_PAD}px`,
                                width: wrapperW,
                                minWidth: wrapperW,
                                height: wrapperH,
                                minHeight: wrapperH,
                                position: 'relative',
                                backgroundColor: '#fff8f0',
                                transition: 'width 0.3s ease, height 0.3s ease',
                            }}
                        >
                            {grid && Array.from({ length: rows }, (_, rowIdx) => {
                                const z = grid.maxZ - rowIdx;
                                return (
                                    <Box
                                        key={z}
                                        sx={{
                                            display: 'flex',
                                            gap: `${GAP}px`,
                                            mb: rowIdx < rows - 1 ? `${GAP}px` : 0,
                                        }}
                                    >
                                        {Array.from({ length: cols }, (_, colIdx) => {
                                            const x = colIdx;
                                            const space = grid.map.get(`${x},${z}`);

                                            if (!space) {
                                                return (
                                                    <Box
                                                        key={`${x}-${z}`}
                                                        sx={{
                                                            width: CELL_W,
                                                            height: CELL_H,
                                                            flexShrink: 0,
                                                        }}
                                                    />
                                                );
                                            }

                                            return (
                                                <Tooltip
                                                    key={space.id}
                                                    title={`Coordenadas: (${space.position.x}, ${space.position.y}, ${space.position.z}) · Cap: ${space.maxSpots}`}
                                                    arrow
                                                    placement="top"
                                                >
                                                <Box
                                                    sx={{
                                                        width: CELL_W,
                                                        height: CELL_H,
                                                        flexShrink: 0,
                                                        border: '2px solid #333',
                                                        borderRadius: 1,
                                                        backgroundColor: '#fff',
                                                        display: 'flex',
                                                        flexDirection: 'column',
                                                        alignItems: 'center',
                                                        justifyContent: 'center',
                                                        position: 'relative',
                                                        cursor: 'default',
                                                        '&:hover .delete-btn': { opacity: 1 },
                                                    }}
                                                >
                                                    <Typography
                                                        variant="body2"
                                                        sx={{ fontWeight: 'bold', lineHeight: 1.2 }}
                                                    >
                                                        {grid.numberMap.get(`${x},${z}`)}
                                                    </Typography>
                                                    <Typography variant="caption" color="text.secondary" sx={{ fontSize: '0.65rem', lineHeight: 1 }}>
                                                        Cap: {space.maxSpots}
                                                    </Typography>

                                                    <IconButton
                                                        className="delete-btn"
                                                        size="small"
                                                        color="error"
                                                        onClick={() => setDeleteSpaceId(space.id)}
                                                        sx={{
                                                            position: 'absolute',
                                                            top: -8,
                                                            right: -8,
                                                            opacity: 0,
                                                            transition: 'opacity 0.15s',
                                                            backgroundColor: '#fff',
                                                            boxShadow: 1,
                                                            p: 0.25,
                                                            '&:hover': { backgroundColor: '#fee' },
                                                        }}
                                                    >
                                                        <Delete sx={{ fontSize: 14 }} />
                                                    </IconButton>
                                                </Box>
                                                </Tooltip>
                                            );
                                        })}
                                    </Box>
                                );
                            })}
                        </Box>
                    </Box>
                </>
            )}

            <SpaceDialogComponent
                open={addDialogOpen}
                zoneWidth={zoneWidth}
                zoneHeight={zoneHeight}
                existingSpaces={zone.spaces}
                onConfirm={handleAddSpace}
                onCancel={() => setAddDialogOpen(false)}
            />

            <SpaceBulkDialogComponent
                open={bulkDialogOpen}
                existingSpaces={zone.spaces}
                onConfirm={handleBulkAdd}
                onCancel={() => setBulkDialogOpen(false)}
            />

            <ConfirmDialogComponent
                open={!!deleteSpaceId}
                title="Eliminar espacio"
                message={deleteSpace
                    ? (() => {
                        const delGrid = buildGrid(zone.spaces, deleteSpace.position.y);
                        const delNum = delGrid.numberMap.get(`${deleteSpace.position.x},${deleteSpace.position.z}`) ?? '?';
                        return `¿Eliminar el espacio ${delNum} del estante ${directionLabel(deleteSpace.position.y)}?`;
                    })()
                    : ''
                }
                confirmText="Eliminar"
                cancelText="Cancelar"
                confirmColor="error"
                onConfirm={handleDeleteSpace}
                onCancel={() => setDeleteSpaceId(null)}
            />
        </Paper>
    );
};

