import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { Tax } from "../types/ProductTypes.ts";
import { HttpService } from "../../commons/services/HttpService.ts";

export const TaxService = {
    getTaxes: async (session: CustomSession | null): Promise<Tax[]> => {
        return await HttpService.apiv1({
            endpoint: '/taxes',
            method: 'GET'
        }, session)
    }
};