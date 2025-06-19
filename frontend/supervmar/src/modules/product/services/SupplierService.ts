import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { Supplier } from "../types/ProductTypes.ts";
import { HttpService } from "../../commons/services/HttpService.ts";

export const SupplierService = {
    getSuppliers: async (session: CustomSession | null): Promise<Supplier[]> => {
        return await HttpService.apiv1({
            endpoint: '/suppliers',
            method: 'GET'
        }, session)
    }
};