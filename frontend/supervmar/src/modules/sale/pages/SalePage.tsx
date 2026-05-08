import { useParams } from "react-router";
import { useEffect, useState } from "react";
import { SaleService } from "../services/SaleService.ts";
import { useSession } from "../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../commons/components/LoadingComponent.tsx";
import type { Sale } from "../types/SaleTypes.ts";
import {
    Box,
    Card,
    CardContent,
    Chip,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Typography,
    Paper,
} from "@mui/material";
import { ErrorSnackbarComponent } from "../../commons/components/ErrorSnackbarComponent.tsx";
import { ApiError } from "../../commons/services/HttpService.ts";

const payMethodLabel = (method: string): string => {
    switch (method) {
        case 'cash': return 'Efectivo';
        case 'card': return 'Tarjeta';
        default: return 'Pendiente';
    }
};

const payMethodColor = (method: string): 'success' | 'info' | 'warning' => {
    switch (method) {
        case 'cash': return 'success';
        case 'card': return 'info';
        default: return 'warning';
    }
};

export const SalePage = () => {
    const { id } = useParams<{ id: string }>();
    const [sale, setSale] = useState<Sale | null>(null);
    const [loading, setLoading] = useState(true);
    const [snackbarError, setSnackbarError] = useState<string | null>(null);
    const { session } = useSession();

    useEffect(() => {
        const fetchSale = async () => {
            if (!id) return;
            try {
                setLoading(true);
                const data = await SaleService.getSale(id, session);
                setSale(data);
            } catch (error) {
                console.log(error);
                const message = error instanceof ApiError ? error.message : 'Error inesperado';
                setSnackbarError(message);
            } finally {
                setLoading(false);
            }
        };
        fetchSale();
    }, [id]);

    if (loading) return <LoadingComponent />;
    if (!sale) return <Typography>Venta no encontrada</Typography>;

    return (
        <Box sx={{ maxWidth: 900, mx: 'auto', p: 2 }}>
            <Typography variant="h5" gutterBottom>
                Detalle de Venta
            </Typography>

            <Card sx={{ mb: 3 }}>
                <CardContent>
                    <Box sx={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 2 }}>
                        <Box>
                            <Typography variant="body2" color="text.secondary">ID</Typography>
                            <Typography variant="body1" sx={{ fontFamily: 'monospace', fontSize: '0.85rem' }}>{sale.id}</Typography>
                        </Box>
                        <Box>
                            <Typography variant="body2" color="text.secondary">Fecha</Typography>
                            <Typography variant="body1">{sale.finishedDate ?? 'En curso'}</Typography>
                        </Box>
                        <Box>
                            <Typography variant="body2" color="text.secondary">Método de pago</Typography>
                            <Chip label={payMethodLabel(sale.payMethod)} color={payMethodColor(sale.payMethod)} size="small" />
                        </Box>
                        <Box>
                            <Typography variant="body2" color="text.secondary">Total</Typography>
                            <Typography variant="h6" color="primary">{sale.totalAmount.toFixed(2)}€</Typography>
                        </Box>
                        <Box>
                            <Typography variant="body2" color="text.secondary">Base imponible</Typography>
                            <Typography variant="body1">{sale.amount.toFixed(2)}€</Typography>
                        </Box>
                        <Box>
                            <Typography variant="body2" color="text.secondary">Impuestos</Typography>
                            <Typography variant="body1">{sale.taxesAmount.toFixed(2)}€</Typography>
                        </Box>
                    </Box>
                </CardContent>
            </Card>

            <Typography variant="h6" gutterBottom>
                Líneas de venta ({sale.lines.length})
            </Typography>

            <TableContainer component={Paper}>
                <Table size="small">
                    <TableHead>
                        <TableRow>
                            <TableCell>Producto</TableCell>
                            <TableCell>EAN</TableCell>
                            <TableCell align="right">Precio</TableCell>
                            <TableCell align="right">IVA</TableCell>
                            <TableCell align="center">Cantidad</TableCell>
                            <TableCell align="right">Subtotal</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {sale.lines.map((line) => (
                            <TableRow key={line.id}>
                                <TableCell>{line.product.name}</TableCell>
                                <TableCell>{line.product.ean}</TableCell>
                                <TableCell align="right">{line.product.price.toFixed(2)}€</TableCell>
                                <TableCell align="right">{line.product.tax.name}</TableCell>
                                <TableCell align="center">{line.quantity}</TableCell>
                                <TableCell align="right">{line.amount.toFixed(2)}€</TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </TableContainer>
            <ErrorSnackbarComponent
                open={!!snackbarError}
                message={snackbarError}
                onClose={() => setSnackbarError(null)}
            />
        </Box>
    );
};
