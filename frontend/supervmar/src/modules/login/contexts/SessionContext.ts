import { createContext, useContext } from "react";
import type { Session } from "@toolpad/core";

export interface CustomSession extends Session {
    id?: string;
    username?: string;
    password?: string;
    isAdmin?: number;
    job?: string | null;
}

export type UserRole = 'admin' | 'cajero' | 'reponedor' | 'encargado' | 'default';

export const getUserRole = (session: CustomSession | null): UserRole => {
    if (!session) return 'default';
    if (session.isAdmin === 1) return 'admin';

    const job = session.job?.toLowerCase() ?? '';
    if (job.includes('cajero')) return 'cajero';
    if (job.includes('reponedor')) return 'reponedor';
    if (job.includes('encargado')) return 'encargado';

    return 'default';
};

interface SessionContextProps {
    session: CustomSession | null;
    setSession: (session: CustomSession | null) => void;
    logout: () => void;
}

export const SessionContext = createContext<SessionContextProps>({
    session: {},
    setSession: () => {},
    logout: () => {},
})

export const useSession = () => {
    return useContext(SessionContext);
}