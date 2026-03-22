import { HttpService } from "../../commons/services/HttpService.ts";
import type { CustomSession } from "../../login/contexts/SessionContext.ts";

export interface ProductAllocation {
    product: {
        id: string;
        name: string;
        stock: number;
        image: string;
    };
    space: {
        id: string;
        position: { x: number; y: number; z: number };
        maxSpots: number;
        zone: {
            id: string;
            name: string;
        };
    };
    quantity: number;
}

export const AllocationService = {
    getAllocations: async (session: CustomSession | null): Promise<ProductAllocation[]> => {
        return await HttpService.apiv1({
            endpoint: '/products_allocations',
            method: 'GET'
        }, session);
    },

    assignProduct: async (
        idSpace: string,
        productId: string,
        quantity: number,
        session: CustomSession | null
    ): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/product_allocation/${idSpace}`,
            method: 'PUT',
            body: { product: productId, quantity }
        }, session);
    },

    removeAllocation: async (idSpace: string, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/product_allocation/${idSpace}`,
            method: 'DELETE'
        }, session);
    },
};

