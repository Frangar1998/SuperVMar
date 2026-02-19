import { type CustomSession } from "../../login/contexts/SessionContext.ts";

interface ApiProps {
    endpoint: string;
    method: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';
    body?: any;
    queryParams?: Record<string, string>;
}

export class ApiError extends Error {
    public readonly code: string;
    public readonly statusCode: number;

    constructor(message: string, code: string, statusCode: number) {
        super(message);
        this.name = 'ApiError';
        this.code = code;
        this.statusCode = statusCode;
    }
}

const request = async (apiUrl: string, apiProps: ApiProps, session: CustomSession | null) => {
    const {endpoint, method, body} = apiProps;

    const AUTH_HEADERS = new Headers();
    AUTH_HEADERS.append('auth-user', session?.username ?? '');
    AUTH_HEADERS.append('auth-password', session?.password ?? '');

    const url = apiUrl + endpoint;
    const requestOptions: RequestInit = {
        method: method,
        headers: AUTH_HEADERS,
    };

    switch (method) {
        case 'GET':
            break;
        case 'POST':
        case 'PUT':
        case 'PATCH':
        case 'DELETE':
            if (body) {
                if (body instanceof FormData) {
                    requestOptions.body = body;
                } else {
                    requestOptions.body = JSON.stringify(body);
                }
            }
            break;
        default:
            throw new Error(`Invalid HTTP method: ${method}`);

    }
    const response = await fetch(
        url,
        requestOptions
    );
    if (!response.ok) {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            const errorBody = await response.json();
            throw new ApiError(
                errorBody.message || response.statusText,
                errorBody.code || 'unknown_error',
                response.status
            );
        }
        throw new ApiError(response.statusText, 'unknown_error', response.status);
    }

    const contentType = response.headers.get("content-type");

    if (contentType && contentType.includes("application/json")) {
        return await response.json();
    } else {
        return { success: true };
    }

};

export const HttpService = {
    api: async (apiProps: ApiProps, session: CustomSession | null) => {
        const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;
        return await request(API_BASE_URL, apiProps, session);
    },

    apiv1: async (apiProps: ApiProps, session: CustomSession | null) => {
        const API_BASE_URL_V1 = import.meta.env.VITE_API_BASE_URL_V1;
        return await request(API_BASE_URL_V1, apiProps, session);
    }
};

