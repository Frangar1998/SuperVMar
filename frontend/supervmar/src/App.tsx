import { type CustomSession, getUserRole, SessionContext } from "./modules/login/contexts/SessionContext.ts";
import { useCallback, useEffect, useMemo, useState } from "react";
import type { Navigation } from "@toolpad/core";
import { ReactRouterAppProvider } from "@toolpad/core/react-router";
import BarChartIcon from '@mui/icons-material/BarChart';
import StorefrontIcon from '@mui/icons-material/Storefront';
import InventoryIcon from '@mui/icons-material/Inventory';
import PeopleIcon from '@mui/icons-material/People';
import PointOfSaleIcon from '@mui/icons-material/PointOfSale';
import { useNavigate, Outlet } from "react-router";
import { createTheme } from "@mui/material";
import { setOnUnauthorized } from "./modules/commons/services/HttpService.ts";

const FULL_NAVIGATION: Navigation = [
    { title: 'Dashboard', icon: <BarChartIcon/> },
    { kind: 'divider' },
    { segment: 'supermercado', title: 'Supermercado', icon: <StorefrontIcon /> },
    { kind: 'divider' },
    {
        segment: 'productos', title: 'Productos', icon: <InventoryIcon />,
        children: [
            { segment: 'catalogo', title: 'Catalogo' },
            { segment: 'asignaciones', title: 'Asignación de productos' },
            { segment: 'categorias', title: 'Categorias' },
            { segment: 'proveedores', title: 'Proveedores' },
            { segment: 'iva', title: 'IVA' },
            { segment: 'reposiciones', title: 'Reposiciones' },
            { segment: 'recepcion', title: 'Recepción de mercancía' },
        ]
    },
    { kind: 'divider' },
    {
        segment: 'ventas', title: 'Ventas', icon: <PointOfSaleIcon />,
        children: [
            { segment: 'listado', title: 'Listado' },
            { segment: 'caja', title: 'Vista Caja' },
        ]
    },
    { kind: 'divider' },
    {
        segment: 'usuarios', title: 'Usuarios', icon: <PeopleIcon />,
        children: [
            { segment: 'trabajos', title: 'Trabajos' },
            { segment: 'lista', title: 'Listado de usuarios' },
        ]
    },
];

const ENCARGADO_NAVIGATION: Navigation = [
    { title: 'Dashboard', icon: <BarChartIcon/> },
    { kind: 'divider' },
    { segment: 'supermercado', title: 'Supermercado', icon: <StorefrontIcon /> },
    { kind: 'divider' },
    {
        segment: 'productos', title: 'Productos', icon: <InventoryIcon />,
        children: [
            { segment: 'catalogo', title: 'Catalogo' },
            { segment: 'asignaciones', title: 'Asignación de productos' },
            { segment: 'categorias', title: 'Categorias' },
            { segment: 'proveedores', title: 'Proveedores' },
            { segment: 'iva', title: 'IVA' },
            { segment: 'reposiciones', title: 'Reposiciones' },
            { segment: 'recepcion', title: 'Recepción de mercancía' },
        ]
    },
    { kind: 'divider' },
    {
        segment: 'ventas', title: 'Ventas', icon: <PointOfSaleIcon />,
        children: [
            { segment: 'listado', title: 'Listado' },
        ]
    },
    { kind: 'divider' },
    {
        segment: 'usuarios', title: 'Usuarios', icon: <PeopleIcon />,
        children: [
            { segment: 'trabajos', title: 'Trabajos' },
            { segment: 'lista', title: 'Listado de usuarios' },
        ]
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

    const logout = useCallback(() => {
        setSession(null);
        localStorage.removeItem('session');
        navigate('/login');
    }, [navigate]);

    useEffect(() => {
        setOnUnauthorized(logout);
    }, [logout]);

    const sessionContextValue = useMemo(() => ({
        session,
        setSession: (newSession: CustomSession | null) => {
            setSession(newSession);
            if (newSession) {
                localStorage.setItem('session', JSON.stringify(newSession));
            } else {
                localStorage.removeItem('session');
            }
        },
        logout,
    }), [session, logout]);

    const role = useMemo(() => getUserRole(session), [session]);

    const navigation = useMemo(() => {
        if (role === 'encargado') return ENCARGADO_NAVIGATION;
        return FULL_NAVIGATION;
    }, [role]);

    const toolpadSession = useMemo(() => {
        if (!session) return null;
        return {
            ...session,
            user: {
                name: session.username ?? 'Usuario',
            },
        };
    }, [session]);

    return (
        <SessionContext.Provider value={sessionContextValue}>
            <ReactRouterAppProvider
                navigation={navigation}
                branding={BRANDING}
                theme={THEME}
                session={toolpadSession}
                authentication={{ signIn: () => {}, signOut: logout }}
                localeText={{ accountSignOutLabel: 'Cerrar sesión' }}
            >
                <Outlet/>
            </ReactRouterAppProvider>
        </SessionContext.Provider>
    );
};