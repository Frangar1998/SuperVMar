import type { SupplierTableHeader, SupplierTable } from "../../types/ProductTypes.ts";

export const SUPPLIERS_TABLE_HEADERS: SupplierTableHeader[] = [
    {
        id: 'name' as keyof SupplierTable,
        numeric: false,
        label: 'Nombre'
    },
    {
        id: 'phone' as keyof SupplierTable,
        numeric: false,
        label: 'Teléfono'
    },
    {
        id: 'email' as keyof SupplierTable,
        numeric: false,
        label: 'Email'
    },
    {
        id: 'contact' as keyof SupplierTable,
        numeric: false,
        label: 'Contacto'
    },
];

