import { createContext, useContext } from "react";
import type { Session } from "@toolpad/core";

export interface CustomSession extends Session {
    id?: string;
    username?: string;
    password?: string;
    isAdmin?: number;
}

interface SessionContextProps {
    session: CustomSession | null;
    setSession: (session: CustomSession | null) => void;
}

export const SessionContext = createContext<SessionContextProps>({
    session: {},
    setSession: () => {},
})

export const useSession = () => {
    return useContext(SessionContext);
}