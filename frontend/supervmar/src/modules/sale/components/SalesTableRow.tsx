import type { TableData } from "../../commons/components/Table/TableData.ts";
import type { SaleTable } from "../types/SaleTypes.ts";
import TableCell from "@mui/material/TableCell";
import Chip from "@mui/material/Chip";
import LocalAtmIcon from "@mui/icons-material/LocalAtm";
import CreditCardIcon from "@mui/icons-material/CreditCard";
import HourglassEmptyIcon from "@mui/icons-material/HourglassEmpty";

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

const payMethodIcon = (method: string) => {
    switch (method) {
        case 'cash': return <LocalAtmIcon fontSize="small" />;
        case 'card': return <CreditCardIcon fontSize="small" />;
        default: return <HourglassEmptyIcon fontSize="small" />;
    }
};

export const SalesTableRow = (row: TableData<SaleTable>) => (
    <>
        <TableCell align="left">{row.data.finishedDate || 'En curso'}</TableCell>
        <TableCell align="right">{row.data.totalAmount.toFixed(2)}€</TableCell>
        <TableCell align="right">{row.data.amount.toFixed(2)}€</TableCell>
        <TableCell align="right">{row.data.taxes.toFixed(2)}€</TableCell>
        <TableCell align="left">
            <Chip
                icon={payMethodIcon(row.data.payMethod)}
                label={payMethodLabel(row.data.payMethod)}
                color={payMethodColor(row.data.payMethod)}
                size="small"
            />
        </TableCell>
        <TableCell align="right">{row.data.linesCount}</TableCell>
    </>
);
