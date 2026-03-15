import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { Supermarket, SupermarketFormData } from "../types/SupermarketTypes.ts";
import { HttpService } from "../../commons/services/HttpService.ts";
import { v7 } from "uuid";

export const SupermarketService = {
    getSupermarkets: async (session: CustomSession | null): Promise<Supermarket[]> => {
        return await HttpService.apiv1({
            endpoint: '/supermarkets',
            method: 'GET'
        }, session);
    },

    getSupermarket: async (id: string, session: CustomSession | null): Promise<Supermarket> => {
        return await HttpService.apiv1({
            endpoint: `/supermarket/${id}`,
            method: 'GET'
        }, session);
    },

    createSupermarket: async (data: SupermarketFormData, session: CustomSession | null): Promise<void> => {
        const id = v7();
        return await HttpService.apiv1({
            endpoint: `/supermarket/${id}`,
            method: 'PUT',
            body: data
        }, session);
    },

    updateSupermarket: async (id: string, data: SupermarketFormData, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/supermarket/${id}`,
            method: 'PUT',
            body: data
        }, session);
    },

    deleteSupermarket: async (id: string, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/supermarket/${id}`,
            method: 'DELETE'
        }, session);
    },
};

