import { useState, useEffect, useMemo } from "react";
import {
    Dialog, DialogActions, DialogContent, DialogTitle,
    TextField, Typography, MenuItem, List, ListItemButton,
    ListItemAvatar, ListItemText, Avatar, Chip, Divider,
    InputAdornment, Alert
} from "@mui/material";
import { Search, Inventory2 } from "@mui/icons-material";
import Box from "@mui/material/Box";
import Grid from "@mui/material/Grid";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { ImageComponent } from "../../../commons/components/ImageComponent.tsx";
import type { Product, Category } from "../../types/ProductTypes.ts";
import type { ProductAllocation } from "../../services/AllocationService.ts";
import type { SpaceFormData } from "../../../supermarket/types/SupermarketTypes.ts";
import { directionLabel } from "../../../supermarket/types/SupermarketTypes.ts";

interface ProductAssignmentDialogProps {
    open: boolean;
    space: SpaceFormData | null;
    spaceNumber: number;
    currentAllocation: ProductAllocation | null;
    products: Product[];
    categories: Category[];
    onAssign: (spaceId: string, productId: string, quantity: number) => Promise<void>;
    onRemove: (spaceId: string) => Promise<void>;
    onClose: () => void;
}

export const ProductAssignmentDialogComponent = ({
    open, space, spaceNumber, currentAllocation, products, categories, onAssign, onRemove, onClose
}: ProductAssignmentDialogProps) => {
    const [search, setSearch] = useState('');
    const [categoryFilter, setCategoryFilter] = useState('all');
    const [selectedProductId, setSelectedProductId] = useState<string | null>(null);
    const [quantity, setQuantity] = useState('1');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (open) {
            setSearch('');
            setCategoryFilter('all');
            setSelectedProductId(currentAllocation?.product.id ?? null);
            setQuantity(currentAllocation?.quantity.toString() ?? '1');
            setIsSubmitting(false);
            setError(null);
        }
    }, [open, currentAllocation]);

    const filteredProducts = useMemo(() => {
        return products.filter(p => {
            if (!p.active) return false;
            if (categoryFilter !== 'all' && p.category?.id !== categoryFilter) return false;
            if (search.trim()) {
                const q = search.toLowerCase();
                return p.name.toLowerCase().includes(q) || p.ean.toLowerCase().includes(q);
            }
            return true;
        });
    }, [products, search, categoryFilter]);

    const selectedProduct = products.find(p => p.id === selectedProductId);

    const handleAssign = async () => {
        if (!space || !selectedProductId) return;
        const qty = parseInt(quantity);
        if (isNaN(qty) || qty < 0) {
            setError('La cantidad debe ser al menos 0');
            return;
        }
        if (space.maxSpots && qty > space.maxSpots) {
            setError(`La cantidad no puede superar la capacidad del espacio (${space.maxSpots})`);
            return;
        }

        try {
            setIsSubmitting(true);
            setError(null);
            await onAssign(space.id, selectedProductId, qty);
            onClose();
        } catch (e: unknown) {
            const msg = e instanceof Error ? e.message : 'Error al asignar el producto';
            setError(msg);
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleRemove = async () => {
        if (!space) return;
        try {
            setIsSubmitting(true);
            setError(null);
            await onRemove(space.id);
            onClose();
        } catch (e: unknown) {
            const msg = e instanceof Error ? e.message : 'Error al eliminar la asignación';
            setError(msg);
        } finally {
            setIsSubmitting(false);
        }
    };

    if (!space) return null;

    return (
        <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
            <DialogTitle>
                Asignar producto — Espacio {spaceNumber}
                <Typography variant="body2" color="text.secondary">
                    Estante {directionLabel(space.position.y)} · Capacidad: {space.maxSpots}
                </Typography>
            </DialogTitle>
            <DialogContent>
                {currentAllocation && (
                    <Alert
                        severity="info"
                        sx={{ mb: 2 }}
                        action={
                            <ButtonComponent
                                text="Quitar"
                                type="button"
                                variant="text"
                                color="error"
                                size="small"
                                onClick={handleRemove}
                                disabled={isSubmitting}
                            />
                        }
                    >
                        <Typography variant="body2">
                            Asignado: <strong>{currentAllocation.product.name}</strong> (Qty: {currentAllocation.quantity})
                        </Typography>
                    </Alert>
                )}

                <Grid container spacing={2} sx={{ mb: 2 }}>
                    <Grid size={{ xs: 5 }}>
                        <TextField
                            select
                            fullWidth
                            size="small"
                            label="Categoría"
                            value={categoryFilter}
                            onChange={(e) => setCategoryFilter(e.target.value)}
                            sx={{ mt: 1 }}
                        >
                            <MenuItem value="all">Todas las categorías</MenuItem>
                            {categories.map(c => (
                                <MenuItem key={c.id} value={c.id}>{c.name}</MenuItem>
                            ))}
                        </TextField>
                    </Grid>
                    <Grid size={{ xs: 7 }}>
                        <TextField
                            fullWidth
                            size="small"
                            placeholder="Buscar por nombre o EAN..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            sx={{ mt: 1 }}
                            slotProps={{
                                input: {
                                    startAdornment: (
                                        <InputAdornment position="start">
                                            <Search fontSize="small" />
                                        </InputAdornment>
                                    ),
                                },
                            }}
                        />
                    </Grid>
                </Grid>

                <Box sx={{
                    border: '1px solid #e0e0e0',
                    borderRadius: 1,
                    maxHeight: 300,
                    overflow: 'auto',
                    mb: 2,
                }}>
                    {filteredProducts.length === 0 ? (
                        <Box sx={{ textAlign: 'center', py: 3, color: 'text.secondary' }}>
                            <Typography variant="body2">No se encontraron productos.</Typography>
                        </Box>
                    ) : (
                        <List dense disablePadding>
                            {filteredProducts.map((product, idx) => (
                                <Box key={product.id}>
                                    {idx > 0 && <Divider />}
                                    <ListItemButton
                                        selected={selectedProductId === product.id}
                                        onClick={() => setSelectedProductId(product.id)}
                                        sx={{
                                            '&.Mui-selected': {
                                                backgroundColor: '#e3f2fd',
                                                borderLeft: '3px solid #1976d2',
                                            },
                                        }}
                                    >
                                        <ListItemAvatar>
                                            {product.image ? (
                                                <Avatar variant="rounded" sx={{ width: 40, height: 40 }}>
                                                    <ImageComponent
                                                        path={product.image}
                                                        altImage={product.name}
                                                        style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                                                    />
                                                </Avatar>
                                            ) : (
                                                <Avatar variant="rounded" sx={{ width: 40, height: 40, bgcolor: '#e0e0e0' }}>
                                                    <Inventory2 fontSize="small" />
                                                </Avatar>
                                            )}
                                        </ListItemAvatar>
                                        <ListItemText
                                            primary={
                                                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                                                    <Typography variant="body2" sx={{ fontWeight: 'bold' }}>
                                                        {product.name}
                                                    </Typography>
                                                    <Chip
                                                        label={product.category?.name ?? 'Sin categoría'}
                                                        size="small"
                                                        variant="outlined"
                                                        sx={{ fontSize: '0.7rem', height: 20 }}
                                                    />
                                                </Box>
                                            }
                                            secondary={
                                                <Typography variant="caption" color="text.secondary">
                                                    EAN: {product.ean} · Stock: {product.stock} · {product.price}€
                                                </Typography>
                                            }
                                        />
                                    </ListItemButton>
                                </Box>
                            ))}
                        </List>
                    )}
                </Box>

                {selectedProduct && (
                    <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
                        <Chip
                            label={selectedProduct.name}
                            color="primary"
                            variant="filled"
                            sx={{ fontWeight: 'bold' }}
                        />
                        <TextField
                            size="small"
                            label="Cantidad"
                            type="number"
                            value={quantity}
                            onChange={(e) => { setQuantity(e.target.value); setError(null); }}
                            sx={{ width: 120 }}
                            slotProps={{ htmlInput: { min: 1, max: space.maxSpots } }}
                        />
                    </Box>
                )}

                {error && (
                    <Alert severity="error" sx={{ mt: 2 }}>{error}</Alert>
                )}
            </DialogContent>
            <DialogActions>
                <ButtonComponent
                    text="Cancelar"
                    type="button"
                    variant="text"
                    color="primary"
                    size="medium"
                    onClick={onClose}
                    disabled={isSubmitting}
                />
                <ButtonComponent
                    text={isSubmitting ? "Guardando..." : "Asignar"}
                    type="button"
                    variant="contained"
                    color="primary"
                    size="medium"
                    onClick={handleAssign}
                    disabled={!selectedProductId || isSubmitting}
                />
            </DialogActions>
        </Dialog>
    );
};


