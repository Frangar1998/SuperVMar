import { api } from "../../commons/services/Http.ts";
import type { CustomSession } from "../../login/contexts/SessionContext.ts";

const URI = '/login';

interface LoginProps {
    username: string;
    password: string;
}

export const login = async (loginProps: LoginProps, session: CustomSession | null) => {
    return await api({
        uri: URI,
        method: 'POST',
        body: {
            username: loginProps.username,
            password: loginProps.password
        }
    }, session);
}