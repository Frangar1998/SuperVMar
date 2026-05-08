import { TableComponent } from "../../commons/components/Table/TableComponent.tsx";
import type { TableData } from "../../commons/components/Table/TableData.ts";
import { useNavigate } from "react-router";
import { useEffect, useMemo, useState } from "react";
import { SaleService } from "../services/SaleService.ts";
import { useSession } from "../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../commons/components/LoadingComponent.tsx";
import type { SaleTable } from "../types/SaleTypes.ts";
import { SALES_TABLE_HEADERS } from "../components/SalesTableHeaders.ts";
import { SalesTableRow } from "../components/SalesTableRow.tsx";
import { Box, FormControl, InputLabel, MenuItem, Select } from "@mui/material";
import { Visibility } from "@mui/icons-material";
import { ErrorSnackbarComponent } from "../../commons/components/ErrorSnackbarComponent.tsx";
import { ApiError } from "../../commons/services/HttpService.ts";

export const SalesPage = () => {
    const navigate = useNavigate();
    const [sales, setSales] = useState<SaleTable[]>([]);
    const [loading, setLoading] = useState(true);
    const [payMethodFilter, setPayMethodFilter] = useState<string>('all');
    const { session } = useSession();
    const [snackbarError, setSnackbarError] = useState<string | null>(null);

    const fetchSales = async () => {
        try {
            setLoading(true);
            const data = await SaleService.getSales(session);
            setSales(data.sales.map((sale: any) => ({
                id: sale.id,
                totalAmount: sale.totalAmount,
                amount: sale.amount,
                taxes: sale.taxes,
                payMethod: sale.payMethod,
                finishedDate: sale.finishedDate ?? '',
                linesCount: sale.lines?.length ?? 0,
            })));
        } catch (error) {
            console.log(error);
            const message = error instanceof ApiError ? error.message : 'Error inesperado';
            setSnackbarError(message);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchSales();
    }, []);

    const filteredSales = useMemo(() => {
        if (payMethodFilter === 'all') return sales;
        return sales.filter((sale) => sale.payMethod === payMethodFilter);
    }, [sales, payMethodFilter]);

    const tableData: TableData<SaleTable>[] = filteredSales.map((sale) => ({
        data: sale,
    }));

    const handleRowClick = (row: TableData<SaleTable>) => {
        navigate(`/ventas/${row.data.id}`);
    };

    if (loading) return <LoadingComponent />;

    return (
        <Box>
            <Box sx={{ display: 'flex', gap: 2, mb: 2, px: 2 }}>
                <FormControl size="small" sx={{ minWidth: 180 }}>
                    <InputLabel id="pay-method-filter-label">Método de pago</InputLabel>
                    <Select
                        labelId="pay-method-filter-label"
                        value={payMethodFilter}
                        label="Método de pago"
                        onChange={(e) => setPayMethodFilter(e.target.value)}
                    >
                        <MenuItem value="all">Todos</MenuItem>
                        <MenuItem value="cash">Efectivo</MenuItem>
                        <MenuItem value="card">Tarjeta</MenuItem>
                        <MenuItem value="none">Pendiente</MenuItem>
                    </Select>
                </FormControl>
            </Box>
            <TableComponent
                tableData={tableData}
                initialSortKey="finishedDate"
                initialSortOrder="desc"
                headers={SALES_TABLE_HEADERS}
                renderRow={SalesTableRow}
                getRowId={(row) => row.data.id}
                onRowClick={handleRowClick}
                actionIcon={<Visibility fontSize="small" />}
                actionTooltip="Ver"
            />
            <ErrorSnackbarComponent
                open={!!snackbarError}
                message={snackbarError}
                onClose={() => setSnackbarError(null)}
            />
        </Box>
    );
};