import type { ProductsTableHeader, ProductTable } from "../../types/ProductTypes.ts";

export const PRODUCTS_TABLE_HEADERS: ProductsTableHeader[] = [
    {
        id: 'image' as keyof ProductTable,
        numeric: false,
        label: ''
    },
    {
        id: 'name' as keyof ProductTable,
        numeric: false,
        label: 'Nombre'
    },
    {
        id: 'ean' as keyof ProductTable,
        numeric: false,
        label: 'Ean'
    },
    {
        id: 'category' as keyof ProductTable,
        numeric: false,
        label: 'Categoría'
    },
    {
        id: 'price' as keyof ProductTable,
        numeric: true,
        label: 'Precio'
    },
    {
        id: 'tax' as keyof ProductTable,
        numeric: false,
        label: 'IVA'
    },
    {
        id: 'stock' as keyof ProductTable,
        numeric: true,
        label: 'Stock'
    },
    {
        id: 'active' as keyof ProductTable,
        numeric: true,
        label: 'Activo'
    },
];