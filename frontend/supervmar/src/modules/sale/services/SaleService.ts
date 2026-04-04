import { HttpService } from "../../commons/services/HttpService.ts";
import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { Sale, SaleProduct } from "../types/SaleTypes.ts";

export const SaleService = {
    searchProductByEan: async (ean: string, session: CustomSession | null): Promise<any> => {
        return await HttpService.apiv1({
            endpoint: `/product?field=ean&value=${encodeURIComponent(ean)}`,
            method: 'GET'
        }, session);
    },

    getSales: async (session: CustomSession | null, date?: string, dateTo?: string): Promise<{ sales: any[] }> => {
        const params = new URLSearchParams();
        if (date) params.set('date', date);
        if (dateTo) params.set('dateTo', dateTo);
        const queryString = params.toString();
        const queryParam = queryString ? `?${queryString}` : '';
        return await HttpService.apiv1({
            endpoint: `/sales${queryParam}`,
            method: 'GET'
        }, session);
    },

    getSale: async (id: string, session: CustomSession | null): Promise<Sale> => {
        return await HttpService.apiv1({
            endpoint: `/sale/${id}`,
            method: 'GET'
        }, session);
    },

    addLine: async (saleId: string, product: SaleProduct, quantity: number, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/sale_line/${saleId}`,
            method: 'PUT',
            body: {
                product: {
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    ean: product.ean,
                    tax: product.tax
                },
                quantity
            }
        }, session);
    },

    finishSale: async (saleId: string, payMethod: 'cash' | 'card', session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/sale_finish/${saleId}`,
            method: 'PATCH',
            body: { payMethod }
        }, session);
    },

    cancelSale: async (saleId: string, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/sale/${saleId}`,
            method: 'DELETE'
        }, session);
    }
};
