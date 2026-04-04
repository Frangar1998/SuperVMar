import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import type { Job } from "../types/UserTypes.ts";
import { HttpService } from "../../commons/services/HttpService.ts";
import { v7 } from "uuid";

export const JobService = {
    getJobs: async (session: CustomSession | null): Promise<Job[]> => {
        const response = await HttpService.apiv1({
            endpoint: '/jobs',
            method: 'GET'
        }, session);
        return response.jobs;
    },

    createJob: async (name: string, session: CustomSession | null): Promise<void> => {
        const id = v7();
        return await HttpService.apiv1({
            endpoint: `/job/${id}`,
            method: 'PUT',
            body: { name }
        }, session);
    },

    updateJob: async (id: string, name: string, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/job/${id}`,
            method: 'PUT',
            body: { name }
        }, session);
    },

    deleteJob: async (id: string, session: CustomSession | null): Promise<void> => {
        return await HttpService.apiv1({
            endpoint: `/job/${id}`,
            method: 'DELETE'
        }, session);
    },
};
