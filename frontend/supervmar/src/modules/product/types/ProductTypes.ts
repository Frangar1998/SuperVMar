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

export interface Tax {
    id: string;
    name: string;
    percent: string | number;
}

export interface Supplier {
    id: string;
    name: string;
}

export interface ProductsTableHeader {
    id: keyof ProductTable;
    numeric: boolean;
    label: string;
}
