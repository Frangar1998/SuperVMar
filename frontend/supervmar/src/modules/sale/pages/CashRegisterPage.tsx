import { useState, useRef, useEffect, useCallback } from 'react';
import {
    Box, Typography, Paper, Table, TableBody, TableCell, TableContainer,
    TableHead, TableRow, IconButton, Divider, TextField,
    Dialog, DialogTitle, DialogContent, DialogActions, Button, Snackbar, Alert
} from '@mui/material';
import AddCircleOutlineIcon from '@mui/icons-material/AddCircleOutline';
import RemoveCircleOutlineIcon from '@mui/icons-material/RemoveCircleOutline';
import DeleteIcon from '@mui/icons-material/Delete';
import PointOfSaleIcon from '@mui/icons-material/PointOfSale';
import CancelIcon from '@mui/icons-material/Cancel';
import CreditCardIcon from '@mui/icons-material/CreditCard';
import LocalAtmIcon from '@mui/icons-material/LocalAtm';
import { v7 } from 'uuid';
import { SaleService } from '../services/SaleService.ts';
import { useSession } from '../../login/contexts/SessionContext.ts';
import type { Sale, SaleLine, SaleProduct } from '../types/SaleTypes.ts';
import { ConfirmDialogComponent } from '../../commons/components/ConfirmDialogComponent.tsx';
import { ErrorSnackbarComponent } from '../../commons/components/ErrorSnackbarComponent.tsx';

const SALE_ID_KEY = 'cashRegister_saleId';

export const CashRegisterPage = () => {
    const { session } = useSession();
    
    const [saleId, setSaleId] = useState<string>(() => {
        return localStorage.getItem(SALE_ID_KEY) ?? v7();
    });
    const [sale, setSale] = useState<Sale | null>(null);
    const [eanInput, setEanInput] = useState('');
    const [processing, setProcessing] = useState(false);
    
    const [cancelDialogOpen, setCancelDialogOpen] = useState(false);
    const [paymentDialogOpen, setPaymentDialogOpen] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    
    const eanInputRef = useRef<HTMLInputElement>(null);
    const hadStoredSale = useRef(localStorage.getItem(SALE_ID_KEY) !== null);

    const focusEanInput = useCallback(() => {
        setTimeout(() => {
            eanInputRef.current?.focus();
        }, 100);
    }, []);

    // Persist saleId to localStorage
    useEffect(() => {
        localStorage.setItem(SALE_ID_KEY, saleId);
    }, [saleId]);

    // Recover in-progress sale on mount (only if there was a stored saleId)
    useEffect(() => {
        const recoverSale = async () => {
            if (!hadStoredSale.current) return;
            try {
                const existingSale = await SaleService.getSale(saleId, session);
                if (!existingSale.finishedDate) {
                    setSale(existingSale);
                } else {
                    resetToNewSale();
                }
            } catch {
                // Sale doesn't exist in API, start fresh
            }
        };
        recoverSale();
        focusEanInput();
    }, []); // eslint-disable-line react-hooks/exhaustive-deps

    // Warn before leaving with active sale
    useEffect(() => {
        const handleBeforeUnload = (e: BeforeUnloadEvent) => {
            if (sale && sale.lines.length > 0) {
                e.preventDefault();
            }
        };
        window.addEventListener('beforeunload', handleBeforeUnload);
        return () => window.removeEventListener('beforeunload', handleBeforeUnload);
    }, [sale]);

    const refreshSale = useCallback(async () => {
        try {
            const updatedSale = await SaleService.getSale(saleId, session);
            setSale(updatedSale);
        } catch {
            setSale(null);
        }
    }, [saleId, session]);

    const handleEanSubmit = async () => {
        const ean = eanInput.trim();
        if (!ean || processing) return;

        setProcessing(true);
        setEanInput('');

        try {
            const productData = await SaleService.searchProductByEan(ean, session);
            
            const saleProduct: SaleProduct = {
                id: productData.id,
                name: productData.name,
                price: productData.price,
                ean: productData.ean,
                tax: {
                    id: productData.tax.id,
                    name: productData.tax.name,
                    percent: productData.tax.percent
                }
            };

            await SaleService.addLine(saleId, saleProduct, 1, session);

            await refreshSale();
        } catch (err: any) {
            setError(err?.message || 'Producto no encontrado');
        } finally {
            setProcessing(false);
            focusEanInput();
        }
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleEanSubmit();
        }
    };

    const handleIncrement = async (line: SaleLine) => {
        setProcessing(true);
        try {
            await SaleService.addLine(saleId, line.product, 1, session);
            await refreshSale();
        } catch (err: any) {
            setError(err?.message || 'Error al incrementar');
        } finally {
            setProcessing(false);
            focusEanInput();
        }
    };

    const handleDecrement = async (line: SaleLine) => {
        setProcessing(true);
        try {
            await SaleService.addLine(saleId, line.product, -1, session);
            await refreshSale();
        } catch (err: any) {
            setError(err?.message || 'Error al decrementar');
        } finally {
            setProcessing(false);
            focusEanInput();
        }
    };

    const handleRemoveLine = async (line: SaleLine) => {
        setProcessing(true);
        try {
            await SaleService.addLine(saleId, line.product, -line.quantity, session);
            await refreshSale();
        } catch (err: any) {
            setError(err?.message || 'Error al eliminar línea');
        } finally {
            setProcessing(false);
            focusEanInput();
        }
    };

    const resetToNewSale = useCallback(() => {
        const newId = v7();
        setSaleId(newId);
        setSale(null);
        setEanInput('');
        localStorage.setItem(SALE_ID_KEY, newId);
        focusEanInput();
    }, [focusEanInput]);

    const handleCancelSale = async () => {
        try {
            if (sale) {
                await SaleService.cancelSale(saleId, session);
            }
            resetToNewSale();
        } catch (err: any) {
            setError(err?.message || 'Error al cancelar la venta');
        } finally {
            setCancelDialogOpen(false);
            focusEanInput();
        }
    };

    const handleFinishSale = async (payMethod: 'cash' | 'card') => {
        setProcessing(true);
        try {
            await SaleService.finishSale(saleId, payMethod, session);
            setPaymentDialogOpen(false);
            setSuccessMessage(
                `Venta finalizada correctamente (${payMethod === 'cash' ? 'Efectivo' : 'Tarjeta'}): ${sale?.totalAmount.toFixed(2)} €`
            );
            resetToNewSale();
        } catch (err: any) {
            setError(err?.message || 'Error al finalizar la venta');
        } finally {
            setProcessing(false);
        }
    };

    const lines = sale?.lines ?? [];
    const hasLines = lines.length > 0;

    return (
        <Box sx={{ 
            display: 'flex', 
            height: '100vh', 
            bgcolor: '#f5f5f5',
            overflow: 'hidden'
        }}>
            {/* LEFT PANEL - Products list */}
            <Box sx={{ flex: 7, display: 'flex', flexDirection: 'column', p: 2 }}>
                {/* Header */}
                <Paper sx={{ p: 2, mb: 2, display: 'flex', alignItems: 'center', gap: 2 }}>
                    <PointOfSaleIcon sx={{ fontSize: 32, color: 'primary.main' }} />
                    <Typography variant="h5" fontWeight="bold">
                        SuperVMar - Caja
                    </Typography>
                    {/* EAN Input - always visible for scanner */}
                    <TextField
                        inputRef={eanInputRef}
                        value={eanInput}
                        onChange={(e) => setEanInput(e.target.value)}
                        onKeyDown={handleKeyDown}
                        placeholder="Escanear código de barras..."
                        size="small"
                        disabled={processing}
                        sx={{ ml: 'auto', width: 300 }}
                        autoFocus
                    />
                </Paper>

                {/* Products table */}
                <TableContainer component={Paper} sx={{ flex: 1, overflow: 'auto' }}>
                    <Table stickyHeader>
                        <TableHead>
                            <TableRow>
                                <TableCell sx={{ fontWeight: 'bold', bgcolor: 'primary.main', color: 'white' }}>
                                    Producto
                                </TableCell>
                                <TableCell align="right" sx={{ fontWeight: 'bold', bgcolor: 'primary.main', color: 'white' }}>
                                    Precio Ud.
                                </TableCell>
                                <TableCell align="center" sx={{ fontWeight: 'bold', bgcolor: 'primary.main', color: 'white' }}>
                                    Cantidad
                                </TableCell>
                                <TableCell align="right" sx={{ fontWeight: 'bold', bgcolor: 'primary.main', color: 'white' }}>
                                    Importe
                                </TableCell>
                                <TableCell align="center" sx={{ fontWeight: 'bold', bgcolor: 'primary.main', color: 'white', width: 120 }}>
                                    Acciones
                                </TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {hasLines ? (
                                lines.map((line) => (
                                    <TableRow key={line.id} hover>
                                        <TableCell>
                                            <Typography fontWeight="medium">{line.product.name}</Typography>
                                        </TableCell>
                                        <TableCell align="right">
                                            {line.product.price.toFixed(2)} €
                                        </TableCell>
                                        <TableCell align="center">
                                            <Typography fontWeight="bold" fontSize="1.1rem">
                                                {line.quantity}
                                            </Typography>
                                        </TableCell>
                                        <TableCell align="right">
                                            <Typography fontWeight="bold">
                                                {line.amount.toFixed(2)} €
                                            </Typography>
                                        </TableCell>
                                        <TableCell align="center">
                                            <IconButton
                                                color="success"
                                                onClick={() => handleIncrement(line)}
                                                disabled={processing}
                                                title="Añadir una unidad"
                                                size="small"
                                            >
                                                <AddCircleOutlineIcon />
                                            </IconButton>
                                            <IconButton
                                                color="warning"
                                                onClick={() => handleDecrement(line)}
                                                disabled={processing}
                                                title="Quitar una unidad"
                                                size="small"
                                            >
                                                <RemoveCircleOutlineIcon />
                                            </IconButton>
                                            <IconButton
                                                color="error"
                                                onClick={() => handleRemoveLine(line)}
                                                disabled={processing}
                                                title="Eliminar línea"
                                                size="small"
                                            >
                                                <DeleteIcon />
                                            </IconButton>
                                        </TableCell>
                                    </TableRow>
                                ))
                            ) : (
                                <TableRow>
                                    <TableCell colSpan={5} align="center" sx={{ py: 8 }}>
                                        <PointOfSaleIcon sx={{ fontSize: 64, color: 'grey.400', mb: 2 }} />
                                        <Typography variant="h6" color="text.secondary">
                                            Escanea un producto para comenzar
                                        </Typography>
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </TableContainer>
            </Box>

            {/* RIGHT PANEL - Summary & Actions */}
            <Box sx={{ 
                flex: 3, 
                display: 'flex', 
                flexDirection: 'column', 
                p: 2, 
                pl: 0 
            }}>
                {/* Totals summary */}
                <Paper sx={{ p: 3, mb: 2 }}>
                    <Typography variant="h6" gutterBottom fontWeight="bold">
                        Resumen
                    </Typography>
                    <Divider sx={{ mb: 2 }} />
                    
                    <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 1 }}>
                        <Typography color="text.secondary">Base imponible:</Typography>
                        <Typography>{(sale?.amount ?? 0).toFixed(2)} €</Typography>
                    </Box>
                    
                    <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 1 }}>
                        <Typography color="text.secondary">IVA:</Typography>
                        <Typography>{(sale?.taxesAmount ?? 0).toFixed(2)} €</Typography>
                    </Box>
                    
                    <Divider sx={{ my: 2 }} />
                    
                    <Box sx={{ display: 'flex', justifyContent: 'space-between' }}>
                        <Typography variant="h5" fontWeight="bold">Total:</Typography>
                        <Typography variant="h5" fontWeight="bold" color="primary.main">
                            {(sale?.totalAmount ?? 0).toFixed(2)} €
                        </Typography>
                    </Box>
                    
                    <Box sx={{ mt: 2, display: 'flex', justifyContent: 'space-between' }}>
                        <Typography variant="body2" color="text.secondary">
                            Artículos: {lines.reduce((sum, l) => sum + l.quantity, 0)}
                        </Typography>
                        <Typography variant="body2" color="text.secondary">
                            Líneas: {lines.length}
                        </Typography>
                    </Box>
                </Paper>

                {/* Action buttons - pushed to bottom */}
                <Box sx={{ mt: 'auto', display: 'flex', flexDirection: 'column', gap: 2 }}>
                    <Button
                        variant="contained"
                        color="success"
                        size="large"
                        startIcon={<PointOfSaleIcon />}
                        onClick={() => setPaymentDialogOpen(true)}
                        disabled={!hasLines || processing}
                        sx={{ py: 2, fontSize: '1.1rem' }}
                    >
                        Finalizar venta
                    </Button>
                    
                    <Button
                        variant="outlined"
                        color="error"
                        size="large"
                        startIcon={<CancelIcon />}
                        onClick={() => setCancelDialogOpen(true)}
                        disabled={!hasLines || processing}
                        sx={{ py: 1.5 }}
                    >
                        Cancelar venta
                    </Button>
                </Box>
            </Box>

            {/* Cancel confirmation dialog */}
            <ConfirmDialogComponent
                open={cancelDialogOpen}
                title="Cancelar venta"
                message="¿Estás seguro de que quieres cancelar esta venta? Se eliminarán todos los productos."
                confirmText="Sí, cancelar"
                cancelText="No, volver"
                confirmColor="error"
                onConfirm={handleCancelSale}
                onCancel={() => { setCancelDialogOpen(false); focusEanInput(); }}
            />

            {/* Payment method dialog */}
            <Dialog 
                open={paymentDialogOpen} 
                onClose={() => { setPaymentDialogOpen(false); focusEanInput(); }}
                maxWidth="sm"
                fullWidth
            >
                <DialogTitle>
                    <Typography variant="h5" fontWeight="bold" textAlign="center">
                        Seleccionar método de pago
                    </Typography>
                </DialogTitle>
                <DialogContent>
                    <Typography variant="h4" textAlign="center" color="primary.main" fontWeight="bold" sx={{ my: 2 }}>
                        {(sale?.totalAmount ?? 0).toFixed(2)} €
                    </Typography>
                    <Box sx={{ display: 'flex', gap: 2, mt: 3 }}>
                        <Button
                            variant="contained"
                            color="success"
                            fullWidth
                            onClick={() => handleFinishSale('cash')}
                            disabled={processing}
                            startIcon={<LocalAtmIcon />}
                            sx={{ py: 3, fontSize: '1.2rem' }}
                        >
                            Efectivo
                        </Button>
                        <Button
                            variant="contained"
                            color="primary"
                            fullWidth
                            onClick={() => handleFinishSale('card')}
                            disabled={processing}
                            startIcon={<CreditCardIcon />}
                            sx={{ py: 3, fontSize: '1.2rem' }}
                        >
                            Tarjeta
                        </Button>
                    </Box>
                </DialogContent>
                <DialogActions>
                    <Button 
                        onClick={() => { setPaymentDialogOpen(false); focusEanInput(); }}
                        disabled={processing}
                    >
                        Cancelar
                    </Button>
                </DialogActions>
            </Dialog>

            {/* Error snackbar */}
            <ErrorSnackbarComponent
                open={!!error}
                message={error}
                onClose={() => { setError(null); focusEanInput(); }}
            />

            {/* Success snackbar */}
            <Snackbar
                open={!!successMessage}
                autoHideDuration={4000}
                onClose={() => { setSuccessMessage(null); focusEanInput(); }}
                anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}
            >
                <Alert
                    onClose={() => { setSuccessMessage(null); focusEanInput(); }}
                    severity="success"
                    variant="filled"
                    sx={{ width: '100%' }}
                >
                    {successMessage}
                </Alert>
            </Snackbar>
        </Box>
    );
};
