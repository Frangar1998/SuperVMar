import type { SaleTable } from "../types/SaleTypes.ts";

export const SALES_TABLE_HEADERS: { id: keyof SaleTable; numeric: boolean; label: string }[] = [
    { id: 'finishedDate', numeric: false, label: 'Fecha' },
    { id: 'totalAmount', numeric: true, label: 'Total' },
    { id: 'amount', numeric: true, label: 'Base' },
    { id: 'taxes', numeric: true, label: 'Impuestos' },
    { id: 'payMethod', numeric: false, label: 'Método de pago' },
    { id: 'linesCount', numeric: true, label: 'Líneas' },
];
