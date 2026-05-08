import { useEffect, useMemo, useState } from "react";
import {
    Box,
    IconButton,
    Paper,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TablePagination,
    TableRow,
    TextField,
    Typography,
} from "@mui/material";
import AddCircleIcon from "@mui/icons-material/AddCircle";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { ProductService } from "../../services/ProductService.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import { ErrorSnackbarComponent } from "../../../commons/components/ErrorSnackbarComponent.tsx";
import { ApiError } from "../../../commons/services/HttpService.ts";
import type { ProductTable } from "../../types/ProductTypes.ts";

export const StockReceptionPage = () => {
    const { session } = useSession();
    const [products, setProducts] = useState<ProductTable[]>([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState("");
    const [quantities, setQuantities] = useState<Record<string, number>>({});
    const [snackbarSuccess, setSnackbarSuccess] = useState<string | null>(null);
    const [snackbarError, setSnackbarError] = useState<string | null>(null);
    const [page, setPage] = useState(0);
    const [rowsPerPage, setRowsPerPage] = useState(10);

    const fetchProducts = async () => {
        try {
            setLoading(true);
            const data = await ProductService.getProducts(session);
            setProducts(data);
        } catch (error) {
            const message = error instanceof ApiError ? error.message : "Error inesperado al cargar productos";
            setSnackbarError(message);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchProducts();
    }, []);

    const filteredProducts = useMemo(() => {
        if (!searchTerm) return products;
        const term = searchTerm.toLowerCase();
        return products.filter(
            (p) =>
                p.name.toLowerCase().includes(term) ||
                p.ean.toLowerCase().includes(term)
        );
    }, [products, searchTerm]);

    const paginatedProducts = useMemo(() => {
        const start = page * rowsPerPage;
        return filteredProducts.slice(start, start + rowsPerPage);
    }, [filteredProducts, page, rowsPerPage]);

    const handleSearchChange = (value: string) => {
        setSearchTerm(value);
        setPage(0);
    };

    const handleQuantityChange = (id: string, value: string) => {
        const num = parseInt(value, 10);
        setQuantities((prev) => ({
            ...prev,
            [id]: isNaN(num) || num < 0 ? 0 : num,
        }));
    };

    const handleReceive = async (product: ProductTable) => {
        const quantity = quantities[product.id] || 0;
        if (quantity <= 0) return;

        try {
            await ProductService.receiveStock(product.id, quantity, session);
            setSnackbarSuccess(`Recepción registrada: +${quantity} uds. de "${product.name}"`);
            setQuantities((prev) => ({ ...prev, [product.id]: 0 }));
            await fetchProducts();
        } catch (error) {
            const message = error instanceof ApiError ? error.message : "Error inesperado al registrar recepción";
            setSnackbarError(message);
        }
    };

    if (loading) {
        return <LoadingComponent />;
    }

    return (
        <Box sx={{ p: 3 }}>

            <TextField
                label="Buscar por nombre o EAN"
                variant="outlined"
                size="small"
                fullWidth
                value={searchTerm}
                onChange={(e) => handleSearchChange(e.target.value)}
                sx={{ mb: 3 }}
            />

            <TableContainer component={Paper}>
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell>Nombre</TableCell>
                            <TableCell>EAN</TableCell>
                            <TableCell align="right">Stock actual</TableCell>
                            <TableCell align="right">Cantidad a recibir</TableCell>
                            <TableCell align="center">Acción</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {paginatedProducts.map((product) => (
                            <TableRow key={product.id} hover>
                                <TableCell>{product.name}</TableCell>
                                <TableCell>{product.ean}</TableCell>
                                <TableCell align="right">{product.stock}</TableCell>
                                <TableCell align="right">
                                    <TextField
                                        type="number"
                                        size="small"
                                        value={quantities[product.id] || ""}
                                        onChange={(e) => handleQuantityChange(product.id, e.target.value)}
                                        slotProps={{ htmlInput: { min: 1 } }}
                                        sx={{ width: 100 }}
                                    />
                                </TableCell>
                                <TableCell align="center">
                                    <IconButton
                                        color="primary"
                                        disabled={!quantities[product.id] || quantities[product.id] <= 0}
                                        onClick={() => handleReceive(product)}
                                    >
                                        <AddCircleIcon />
                                    </IconButton>
                                </TableCell>
                            </TableRow>
                        ))}
                        {filteredProducts.length === 0 && (
                            <TableRow>
                                <TableCell colSpan={5} align="center">
                                    No se encontraron productos
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
                <TablePagination
                    component="div"
                    count={filteredProducts.length}
                    page={page}
                    onPageChange={(_, newPage) => setPage(newPage)}
                    rowsPerPage={rowsPerPage}
                    onRowsPerPageChange={(e) => {
                        setRowsPerPage(parseInt(e.target.value, 10));
                        setPage(0);
                    }}
                    rowsPerPageOptions={[5, 10, 25]}
                    labelRowsPerPage="Filas por página"
                />
            </TableContainer>

            <ErrorSnackbarComponent
                open={!!snackbarSuccess}
                message={snackbarSuccess}
                onClose={() => setSnackbarSuccess(null)}
                severity="success"
            />
            <ErrorSnackbarComponent
                open={!!snackbarError}
                message={snackbarError}
                onClose={() => setSnackbarError(null)}
            />
        </Box>
    );
};
