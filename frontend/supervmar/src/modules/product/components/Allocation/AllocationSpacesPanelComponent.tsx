import { useState } from "react";
import { Typography, IconButton, Tooltip, Chip } from "@mui/material";
import { Close, ArrowBack, ArrowForward } from "@mui/icons-material";
import Box from "@mui/material/Box";
import Paper from "@mui/material/Paper";
import type { ZoneFormData, SpaceFormData } from "../../../supermarket/types/SupermarketTypes.ts";
import { directionLabel, SHELF_DIRECTIONS } from "../../../supermarket/types/SupermarketTypes.ts";
import type { Product, Category } from "../../types/ProductTypes.ts";
import type { ProductAllocation } from "../../services/AllocationService.ts";
import { ProductAssignmentDialogComponent } from "./ProductAssignmentDialogComponent.tsx";
import { ImageComponent } from "../../../commons/components/ImageComponent.tsx";

interface AllocationSpacesPanelProps {
    zone: ZoneFormData;
    allocations: Map<string, ProductAllocation>;
    products: Product[];
    categories: Category[];
    onAssign: (spaceId: string, productId: string, quantity: number) => Promise<void>;
    onRemove: (spaceId: string) => Promise<void>;
    onClose: () => void;
}

const CELL_W = 110;
const CELL_H = 80;
const GAP = 6;
const WRAPPER_PAD = 16;

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

export const AllocationSpacesPanelComponent = ({
    zone, allocations, products, categories, onAssign, onRemove, onClose
}: AllocationSpacesPanelProps) => {
    const [currentDirIdx, setCurrentDirIdx] = useState(0);
    const [selectedSpace, setSelectedSpace] = useState<SpaceFormData | null>(null);
    const [selectedSpaceNumber, setSelectedSpaceNumber] = useState(0);

    const activeDirections = getActiveDirections(zone.spaces);
    const currentDirY = activeDirections[currentDirIdx] ?? null;

    const safeIdx = activeDirections.length === 0 ? 0 : Math.min(currentDirIdx, activeDirections.length - 1);
    if (safeIdx !== currentDirIdx && activeDirections.length > 0) {
        setCurrentDirIdx(safeIdx);
    }

    const handlePrev = () => setCurrentDirIdx(i => Math.max(0, i - 1));
    const handleNext = () => setCurrentDirIdx(i => Math.min(activeDirections.length - 1, i + 1));

    const grid = currentDirY !== null ? buildGrid(zone.spaces, currentDirY) : null;

    const cols = grid ? grid.maxX + 1 : 0;
    const rows = grid ? grid.maxZ + 1 : 0;
    const wrapperW = cols * (CELL_W + GAP) - GAP + WRAPPER_PAD * 2;
    const wrapperH = rows * (CELL_H + GAP) - GAP + WRAPPER_PAD * 2;

    const handleSpaceClick = (space: SpaceFormData, spaceNum: number) => {
        setSelectedSpace(space);
        setSelectedSpaceNumber(spaceNum);
    };

    const handleDialogClose = () => {
        setSelectedSpace(null);
    };

    const allocatedCount = zone.spaces.filter(s => allocations.has(s.id)).length;

    return (
        <Paper variant="outlined" sx={{ mt: 2, p: 3 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1.5 }}>
                    <Typography variant="h6">
                        Asignaciones de {zone.name}
                    </Typography>
                    <Chip
                        label={`${allocatedCount} / ${zone.spaces.length} asignados`}
                        size="small"
                        color={allocatedCount === zone.spaces.length ? "success" : "default"}
                        variant="outlined"
                    />
                </Box>
                <Tooltip title="Cerrar panel">
                    <IconButton onClick={onClose} size="small">
                        <Close />
                    </IconButton>
                </Tooltip>
            </Box>

            {activeDirections.length === 0 ? (
                <Box sx={{ textAlign: 'center', py: 4, color: 'text.secondary' }}>
                    <Typography>No hay espacios definidos en esta zona.</Typography>
                    <Typography variant="body2" sx={{ mt: 1 }}>
                        Define espacios en la página del supermercado primero.
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
                        Haz clic en un espacio para asignar o cambiar un producto
                    </Typography>

                    <Box sx={{ display: 'flex', justifyContent: 'center' }}>
                        <Box
                            sx={{
                                display: 'inline-block',
                                border: '3px solid #1976d2',
                                borderRadius: 2,
                                p: `${WRAPPER_PAD}px`,
                                width: wrapperW,
                                minWidth: wrapperW,
                                height: wrapperH,
                                minHeight: wrapperH,
                                position: 'relative',
                                backgroundColor: '#f5f9ff',
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

                                            const allocation = allocations.get(space.id);
                                            const isAllocated = !!allocation;
                                            const isEmpty = isAllocated && allocation.quantity === 0;
                                            const spaceNum = grid.numberMap.get(`${x},${z}`) ?? 0;

                                            const borderColor = !isAllocated ? '#9e9e9e' : isEmpty ? '#c62828' : '#2e7d32';
                                            const bgColor = !isAllocated ? '#fafafa' : isEmpty ? '#ffebee' : '#e8f5e9';
                                            const hoverBg = !isAllocated ? '#e3f2fd' : isEmpty ? '#ffcdd2' : '#c8e6c9';

                                            return (
                                                <Tooltip
                                                    key={space.id}
                                                    title={isAllocated
                                                        ? `${allocation.product.name} — Qty: ${allocation.quantity} / ${space.maxSpots}`
                                                        : `Espacio ${spaceNum} — Vacío (Cap: ${space.maxSpots})`
                                                    }
                                                    arrow
                                                    placement="top"
                                                >
                                                    <Box
                                                        onClick={() => handleSpaceClick(space, spaceNum)}
                                                        sx={{
                                                            width: CELL_W,
                                                            height: CELL_H,
                                                            flexShrink: 0,
                                                            border: `2px solid ${borderColor}`,
                                                            borderRadius: 1,
                                                            backgroundColor: bgColor,
                                                            display: 'flex',
                                                            flexDirection: 'column',
                                                            alignItems: 'center',
                                                            justifyContent: 'center',
                                                            cursor: 'pointer',
                                                            transition: 'all 0.15s',
                                                            '&:hover': {
                                                                borderColor: borderColor,
                                                                backgroundColor: hoverBg,
                                                                transform: 'scale(1.05)',
                                                            },
                                                            overflow: 'hidden',
                                                            px: 0.5,
                                                        }}
                                                    >
                                                        {isAllocated ? (
                                                            <>
                                                                {allocation.product.image && (
                                                                    <ImageComponent
                                                                        path={allocation.product.image}
                                                                        altImage={allocation.product.name}
                                                                        style={{
                                                                            width: 32,
                                                                            height: 32,
                                                                            objectFit: 'cover',
                                                                            borderRadius: 4,
                                                                        }}
                                                                    />
                                                                )}
                                                                <Typography
                                                                    variant="caption"
                                                                    sx={{
                                                                        fontWeight: 'bold',
                                                                        lineHeight: 1.1,
                                                                        textAlign: 'center',
                                                                        overflow: 'hidden',
                                                                        textOverflow: 'ellipsis',
                                                                        whiteSpace: 'nowrap',
                                                                        width: '100%',
                                                                        fontSize: '0.7rem',
                                                                    }}
                                                                >
                                                                    {allocation.product.name}
                                                                </Typography>
                                                                <Typography
                                                                    variant="caption"
                                                                    sx={{
                                                                        fontSize: '0.6rem',
                                                                        lineHeight: 1,
                                                                        color: isEmpty ? 'error.dark' : 'success.dark',
                                                                        fontWeight: isEmpty ? 'bold' : 'normal',
                                                                    }}
                                                                >
                                                                    Cantidad: {allocation.quantity}/{space.maxSpots}
                                                                </Typography>
                                                            </>
                                                        ) : (
                                                            <>
                                                                <Typography
                                                                    variant="body2"
                                                                    sx={{ fontWeight: 'bold', lineHeight: 1.2, color: '#9e9e9e' }}
                                                                >
                                                                    {spaceNum}
                                                                </Typography>
                                                                <Typography variant="caption" sx={{ fontSize: '0.6rem', color: '#bdbdbd' }}>
                                                                    Vacío
                                                                </Typography>
                                                            </>
                                                        )}
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

            <ProductAssignmentDialogComponent
                open={!!selectedSpace}
                space={selectedSpace}
                spaceNumber={selectedSpaceNumber}
                currentAllocation={selectedSpace ? allocations.get(selectedSpace.id) ?? null : null}
                products={products}
                categories={categories}
                onAssign={onAssign}
                onRemove={onRemove}
                onClose={handleDialogClose}
            />
        </Paper>
    );
};

