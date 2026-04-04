import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { User } from "../types/UserTypes.ts";
import { HttpService } from "../../commons/services/HttpService.ts";
import { v7 } from "uuid";

export const UserService = {
    getUsers: async (session: CustomSession | null): Promise<User[]> => {
        const response = await HttpService.apiv1({
            endpoint: '/users',
            method: 'GET'
        }, session);
        return response.users;
    },

    getUser: async (id: string, session: CustomSession | null): Promise<User> => {
        return await HttpService.apiv1({
            endpoint: `/user/${id}`,
            method: 'GET'
        }, session);
    },

    createUser: async (data: {
        username: string;
        userData: { id: string; name: string; surname: string; email: string; phone: string; address: { id: string; name: string; postalCode: string; city: string; number: string; province: string; floor: string; door: string; other: string } };
        isAdmin: number;
        allocations: { idSupermarket: string; idJob: string }[];
        password: string;
        passwordRepeat: string;
    }, session: CustomSession | null): Promise<void> => {
        const id = v7();
        return await HttpService.apiv1({
            endpoint: `/user/${id}`,
            method: 'PUT',
            body: data
        }, session);
    },

    updateUser: async (id: string, data: {
        username: string;
        userData: { id: string; name: string; surname: string; email: string; phone: string; address: { id: string; name: string; postalCode: string; city: string; number: string; province: string; floor: string; door: string; other: string } };
        isAdmin: number;
        allocations: { idSupermarket: string; idJob: string }[];
    }, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/user/${id}`,
            method: 'PUT',
            body: data
        }, session);
    },

    deleteUser: async (id: string, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/user/${id}`,
            method: 'DELETE'
        }, session);
    },

    changePassword: async (id: string, data: {
        currentPassword: string;
        password: string;
        passwordRepeat: string;
    }, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/user/${id}/change-password`,
            method: 'PUT',
            body: data
        }, session);
    },
};
