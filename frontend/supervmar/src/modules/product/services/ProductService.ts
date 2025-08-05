import { HttpService } from "../../commons/services/HttpService.ts";
import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { Product, ProductTable } from "../types/ProductTypes.ts";
import { v7 } from "uuid";

export const ProductService = {
    getProducts: async (session: CustomSession | null): Promise<ProductTable[]> => {
        return await HttpService.apiv1({
            endpoint: '/products',
            method: 'GET'
        }, session);
    },

    createProduct: async (product: Omit<Product, 'id'>, image: File | null, session: CustomSession | null): Promise<Product> => {
        const id = v7();
        const formData = new FormData();
        formData.append('data', JSON.stringify({
            ...product,
            image: undefined
        }));

        if (image) {
            formData.append('image', image);
        }

        return await HttpService.apiv1({
            endpoint: `/product/${id}`,
            method: 'POST',
            body: formData
        }, session);
    },

    getProduct: async (field: string, value: any, session: CustomSession | null): Promise<Product> => {
        return await HttpService.apiv1({
            endpoint: `/product?field=${field}&value=${value}`,
            method: 'GET'
        }, session);
    },

    updateProduct: async (id: string, product: Omit<Product, 'id'>, image: File | null, session: CustomSession | null): Promise<Product> => {
        const formData = new FormData();
        formData.append('data', JSON.stringify({
            ...product,
            image: undefined
        }));

        if (image) {
            formData.append('image', image);
        }

        return await HttpService.apiv1({
            endpoint: `/product/${id}`,
            method: 'PUT',
            body: formData
        }, session);
    },


}