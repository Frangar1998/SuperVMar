import { type CustomSession } from "../../login/contexts/SessionContext.ts";

interface ApiProps {
    uri: string;
    method: string;
    body?: any;
}

const request = async (apiUrl: string, apiProps: ApiProps, session: CustomSession | null) => {
    const AUTH_HEADERS = new Headers();
    AUTH_HEADERS.append('auth-user', session?.username ?? '');
    AUTH_HEADERS.append('auth-password', session?.password ?? '');

    let response = await fetch(
        apiUrl + apiProps.uri,
        {
            method: apiProps.method,
            body: JSON.stringify(apiProps.body),
            headers: AUTH_HEADERS
        });
    if (!response.ok) {
        throw new Error(response.statusText);
    }
    return await response.json();
};

export const api = async (apiProps: ApiProps, session: CustomSession | null) => {
    const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;
    return await request(API_BASE_URL, apiProps, session);
};

export const apiv1 = async (apiProps: ApiProps, session: CustomSession | null) => {
    const API_BASE_URL_V1 = import.meta.env.VITE_API_BASE_URL_V1;
    return await request(API_BASE_URL_V1, apiProps, session);
};

