export type DateFilter = 'today' | 'week' | 'month' | 'year' | 'custom';

export interface SalesKPI {
    totalSales: number;
    totalRevenue: number;
    totalTaxes: number;
    averageSale: number;
    cashCount: number;
    cardCount: number;
    cashRevenue: number;
    cardRevenue: number;
}

export interface DailySalesData {
    date: string;
    sales: number;
    revenue: number;
}

export interface PayMethodData {
    method: string;
    count: number;
    amount: number;
}

export interface ZoneRestockInfo {
    zoneId: string;
    zoneName: string;
    totalSpaces: number;
    emptySpaces: number;
    lowStockSpaces: number;
    status: 'ok' | 'warning' | 'critical';
}
