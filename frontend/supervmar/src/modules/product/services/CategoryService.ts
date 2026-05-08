import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { Category } from "../types/ProductTypes.ts";
import { HttpService } from "../../commons/services/HttpService.ts";
import { v7 } from "uuid";

export const CategoryService = {
    getCategories: async (session: CustomSession | null): Promise<Category[]> => {
        return await HttpService.apiv1({
            endpoint: '/categories',
            method: 'GET'
        }, session);
    },

    createCategory: async (name: string, session: CustomSession | null): Promise<void> => {
        const id = v7();
        return await HttpService.apiv1({
            endpoint: `/category/${id}`,
            method: 'PUT',
            body: { name }
        }, session);
    },

    updateCategory: async (id: string, name: string, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/category/${id}`,
            method: 'PUT',
            body: { name }
        }, session);
    },

    deleteCategory: async (id: string, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/category/${id}`,
            method: 'DELETE'
        }, session);
    },
};