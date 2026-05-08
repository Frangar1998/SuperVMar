import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import { SaleService } from "../../sale/services/SaleService.ts";
import { AllocationService, type ProductAllocation } from "../../product/services/AllocationService.ts";
import type { DateFilter, SalesKPI, DailySalesData, PayMethodData, ZoneRestockInfo } from "../types/DashboardTypes.ts";

function formatDate(date: Date): string {
    return date.toISOString().split('T')[0];
}

function getDateParams(dateFilter: DateFilter, customDateFrom?: string, customDateTo?: string): { date?: string; dateTo?: string } {
    const now = new Date();
    switch (dateFilter) {
        case 'today':
            return { date: formatDate(now) };
        case 'week': {
            const d = new Date(now);
            d.setDate(d.getDate() - 7);
            return { date: formatDate(d) };
        }
        case 'month': {
            const d = new Date(now);
            d.setDate(d.getDate() - 30);
            return { date: formatDate(d) };
        }
        case 'year': {
            const d = new Date(now);
            d.setDate(d.getDate() - 365);
            return { date: formatDate(d) };
        }
        case 'custom':
            return { date: customDateFrom, dateTo: customDateTo };
    }
}

export function computeKPIs(sales: any[]): SalesKPI {
    const finished = sales.filter((s) => s.payMethod !== 'none');
    const totalSales = finished.length;
    const totalRevenue = finished.reduce((sum, s) => sum + s.totalAmount, 0);
    const totalTaxes = finished.reduce((sum, s) => sum + s.taxes, 0);
    const averageSale = totalSales > 0 ? totalRevenue / totalSales : 0;
    const cashSales = finished.filter((s) => s.payMethod === 'cash');
    const cardSales = finished.filter((s) => s.payMethod === 'card');
    const cashCount = cashSales.length;
    const cardCount = cardSales.length;
    const cashRevenue = cashSales.reduce((sum, s) => sum + s.totalAmount, 0);
    const cardRevenue = cardSales.reduce((sum, s) => sum + s.totalAmount, 0);

    return { totalSales, totalRevenue, totalTaxes, averageSale, cashCount, cardCount, cashRevenue, cardRevenue };
}

export function computeDailySales(sales: any[]): DailySalesData[] {
    const map = new Map<string, { sales: number; revenue: number }>();

    for (const sale of sales) {
        if (!sale.finishedDate) continue;
        const date = sale.finishedDate.split(' ')[0];
        const entry = map.get(date) ?? { sales: 0, revenue: 0 };
        entry.sales += 1;
        entry.revenue += sale.totalAmount;
        map.set(date, entry);
    }

    return Array.from(map.entries())
        .map(([date, data]) => ({ date, ...data }))
        .sort((a, b) => a.date.localeCompare(b.date));
}

export function computePayMethodData(sales: any[]): PayMethodData[] {
    const finished = sales.filter((s) => s.payMethod !== 'none');
    const map = new Map<string, { count: number; amount: number }>();

    for (const sale of finished) {
        const entry = map.get(sale.payMethod) ?? { count: 0, amount: 0 };
        entry.count += 1;
        entry.amount += sale.totalAmount;
        map.set(sale.payMethod, entry);
    }

    return Array.from(map.entries()).map(([method, data]) => ({ method, ...data }));
}

export function computeZoneRestockInfo(allocations: ProductAllocation[]): ZoneRestockInfo[] {
    const map = new Map<string, { zoneName: string; totalSpaces: number; emptySpaces: number; lowStockSpaces: number }>();

    for (const alloc of allocations) {
        const zoneId = alloc.space.zone.id;
        const entry = map.get(zoneId) ?? { zoneName: alloc.space.zone.name, totalSpaces: 0, emptySpaces: 0, lowStockSpaces: 0 };
        entry.totalSpaces += 1;
        if (alloc.quantity === 0) {
            entry.emptySpaces += 1;
        } else if (alloc.quantity < 3) {
            entry.lowStockSpaces += 1;
        }
        map.set(zoneId, entry);
    }

    return Array.from(map.entries()).map(([zoneId, data]) => {
        let status: ZoneRestockInfo['status'] = 'ok';
        if (data.emptySpaces > 0) status = 'critical';
        else if (data.lowStockSpaces > 0) status = 'warning';
        return { zoneId, ...data, status };
    });
}

export const DashboardService = {
    fetchSalesData: async (session: CustomSession | null, dateFilter: DateFilter, customDateFrom?: string, customDateTo?: string): Promise<any[]> => {
        const { date, dateTo } = getDateParams(dateFilter, customDateFrom, customDateTo);
        try {
            const response = await SaleService.getSales(session, date, dateTo);
            return response.sales;
        } catch {
            return [];
        }
    },

    fetchAllocations: async (session: CustomSession | null): Promise<ProductAllocation[]> => {
        return await AllocationService.getAllocations(session);
    },
};
