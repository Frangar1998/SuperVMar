export interface SaleTax {
    id: string;
    name: string;
    percent: number;
}

export interface SaleProduct {
    id: string;
    name: string;
    price: number;
    ean: string;
    tax: SaleTax;
}

export interface SaleLine {
    id: string;
    product: SaleProduct;
    amount: number;
    quantity: number;
}

export interface Sale {
    id: string;
    amount: number;
    taxesAmount: number;
    totalAmount: number;
    lines: SaleLine[];
    payMethod: string;
    finishedDate: string | null;
}

export interface SaleTable {
    id: string;
    totalAmount: number;
    amount: number;
    taxes: number;
    payMethod: string;
    finishedDate: string;
    linesCount: number;
}
