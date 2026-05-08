import type { CategoryTableHeader, CategoryTable } from "../../types/ProductTypes.ts";

export const CATEGORIES_TABLE_HEADERS: CategoryTableHeader[] = [
    {
        id: 'name' as keyof CategoryTable,
        numeric: false,
        label: 'Nombre'
    },
];

