import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { Supplier, SupplierFormData } from "../types/ProductTypes.ts";
import { HttpService } from "../../commons/services/HttpService.ts";
import { v7 } from "uuid";

export const SupplierService = {
    getSuppliers: async (session: CustomSession | null): Promise<Supplier[]> => {
        return await HttpService.apiv1({
            endpoint: '/suppliers',
            method: 'GET'
        }, session);
    },

    createSupplier: async (data: SupplierFormData, session: CustomSession | null): Promise<void> => {
        const id = v7();
        return await HttpService.apiv1({
            endpoint: `/supplier/${id}`,
            method: 'PUT',
            body: data
        }, session);
    },

    updateSupplier: async (id: string, data: SupplierFormData, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/supplier/${id}`,
            method: 'PUT',
            body: data
        }, session);
    },

    deleteSupplier: async (id: string, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/supplier/${id}`,
            method: 'DELETE'
        }, session);
    },
};