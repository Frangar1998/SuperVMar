export interface Product {
    id: string;
    image: string;
    name: string;
    ean: string;
    category: Category;
    price: number;
    tax: Tax;
    stock: number;
    active: number;
    supplier: Supplier;
    price_history?: PriceHistory[];
}

export interface ProductFormData {
    name: string;
    ean: string;
    image: string;
    category: Category;
    price: string;
    tax: Tax;
    supplier: Supplier;
    stock: string;
    active: boolean;
}

export interface ProductTable {
    id: string;
    image: string;
    name: string;
    ean: string;
    category: string;
    price: number;
    tax: string;
    stock: number;
    active: number;
}

export interface Category {
    id: string;
    name: string;
}

export interface CategoryTable {
    id: string;
    name: string;
}

export interface CategoryTableHeader {
    id: keyof CategoryTable;
    numeric: boolean;
    label: string;
}

export interface Tax {
    id: string;
    name: string;
    percent: string | number;
}

export interface TaxTable {
    id: string;
    name: string;
    percent: string | number;
}

export interface TaxTableHeader {
    id: keyof TaxTable;
    numeric: boolean;
    label: string;
}

export interface Supplier {
    id: string;
    name: string;
}

export interface PriceHistory {
    id: string;
    price: number;
    startDate: string;
    endDate: string | null;
}

export interface ProductsTableHeader {
    id: keyof ProductTable;
    numeric: boolean;
    label: string;
}
