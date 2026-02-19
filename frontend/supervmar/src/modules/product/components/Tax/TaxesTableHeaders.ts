import type { TaxTableHeader, TaxTable } from "../../types/ProductTypes.ts";

export const TAXES_TABLE_HEADERS: TaxTableHeader[] = [
    {
        id: 'name' as keyof TaxTable,
        numeric: false,
        label: 'Nombre'
    },
    {
        id: 'percent' as keyof TaxTable,
        numeric: true,
        label: 'Porcentaje'
    },
];

