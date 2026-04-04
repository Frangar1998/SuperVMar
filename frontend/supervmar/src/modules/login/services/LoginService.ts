import { HttpService } from "../../commons/services/HttpService.ts";

const ENDPOINT = '/login';

interface LoginProps {
    username: string;
    password: string;
}

export const login = async (loginProps: LoginProps) => {
    return await HttpService.api({
        endpoint: ENDPOINT,
        method: 'POST',
        body: {
            username: loginProps.username,
            password: loginProps.password
        }
    }, null);
}