import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { Tax } from "../types/ProductTypes.ts";
import { HttpService } from "../../commons/services/HttpService.ts";
import { v7 } from "uuid";

export const TaxService = {
    getTaxes: async (session: CustomSession | null): Promise<Tax[]> => {
        return await HttpService.apiv1({
            endpoint: '/taxes',
            method: 'GET'
        }, session);
    },

    createTax: async (name: string, percent: number, session: CustomSession | null): Promise<void> => {
        const id = v7();
        return await HttpService.apiv1({
            endpoint: `/tax/${id}`,
            method: 'PUT',
            body: { name, percent }
        }, session);
    },

    updateTax: async (id: string, name: string, percent: number, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/tax/${id}`,
            method: 'PUT',
            body: { name, percent }
        }, session);
    },

    deleteTax: async (id: string, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/tax/${id}`,
            method: 'DELETE'
        }, session);
    },
};