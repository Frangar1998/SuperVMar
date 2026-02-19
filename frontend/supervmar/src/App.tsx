import { type CustomSession, SessionContext } from "./modules/login/contexts/SessionContext.ts";
import { useCallback, useMemo, useState } from "react";
import type { Navigation } from "@toolpad/core";
import { ReactRouterAppProvider } from "@toolpad/core/react-router";
import DashboardIcon from '@mui/icons-material/Dashboard';
import CategoryIcon from '@mui/icons-material/Category';
import { useNavigate, Outlet } from "react-router";
import { createTheme } from "@mui/material";

const NAVIGATION: Navigation = [
    {
        title: 'Home',
        icon: <DashboardIcon/>
    },
    {kind: 'divider'},
    {
        segment: 'supermercado',
        title: 'Supermercado',
        icon: <CategoryIcon />,
    },
    {kind: 'divider'},
    {
        segment: 'productos',
        title: 'Productos',
        icon: <CategoryIcon />,
        children: [
            {
                segment: 'catalogo',
                title: 'Catalogo',
            },
            {
                segment: 'asignaciones',
                title: 'Asignaciones',
            },
            {
                segment: 'categorias',
                title: 'Categorias',
            },
            {
                segment: 'proveedores',
                title: 'Proveedores',
            },
            {
                segment: 'iva',
                title: 'IVA',
            },
        ]
    },
    {kind: 'divider'},
    {
        segment: 'ventas',
        title: 'Ventas',
        icon: <CategoryIcon />,
    },
    {kind: 'divider'},
    {
        segment: 'usuarios',
        title: 'Usuarios',
        icon: <CategoryIcon />,
    },
];

const BRANDING = {
    title: 'SuperVMar',
    logo: (
        <img src={"/images/supervmar-logo.png"} alt={"SuperVMar"}/>
    )
};

const THEME = createTheme({
    palette: {
        mode: 'light'
    },
    typography: {
        fontFamily: "Arial, Helvetica, sans-serif"
    }
});

export const App = () => {
    const [session, setSession] = useState<CustomSession | null>(() => {
        const storedSession = localStorage.getItem('session');
        return storedSession ? JSON.parse(storedSession) : null;
    });

    const navigate = useNavigate();

    const login = useCallback(() => {
        navigate('/login');
    }, [navigate]);

    const logout = useCallback(() => {
        setSession(null);
        localStorage.removeItem('session');
        navigate('/login');
    }, [navigate]);

    const sessionContextValue = useMemo(() => ({
        session,
        setSession: (newSession: CustomSession | null) => {
            setSession(newSession);
            if (newSession) {
                localStorage.setItem('session', JSON.stringify(newSession));
            } else {
                localStorage.removeItem('session');
            }
        }
    }), [session]);


    return (
        <SessionContext.Provider value={sessionContextValue}>
            <ReactRouterAppProvider
                navigation={NAVIGATION}
                branding={BRANDING}
                theme={THEME}
                session={session}
                authentication={{signIn: login, signOut: logout}}
            >
                <Outlet/>
            </ReactRouterAppProvider>
        </SessionContext.Provider>
    );
};