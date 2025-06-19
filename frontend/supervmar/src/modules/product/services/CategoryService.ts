import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { Category } from "../types/ProductTypes.ts";
import { HttpService } from "../../commons/services/HttpService.ts";

export const CategoryService = {
    getCategories: async (session: CustomSession | null): Promise<Category[]> => {
        return await HttpService.apiv1({
            endpoint: '/categories',
            method: 'GET'
        }, session)
    }
};