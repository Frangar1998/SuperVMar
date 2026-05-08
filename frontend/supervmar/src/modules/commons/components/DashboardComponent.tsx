import { getUserRole, useSession } from "../../login/contexts/SessionContext.ts";
import { Navigate, useLocation, Outlet } from "react-router";
import { DashboardLayout, PageContainer } from "@toolpad/core";
import { MinimalLayout } from "./MinimalLayout.tsx";
import { useEffect } from "react";
import { PushNotificationService } from "../../notification/services/PushNotificationService.ts";

export const DashboardComponent = () => {
    const { session } = useSession();
    const location = useLocation();

    useEffect(() => {
        if (session) {
            PushNotificationService.init(session);
        }
    }, [session]);

    if (!session) {
        const redirect = `/login?callbackUrl=${encodeURIComponent(location.pathname)}`;
        return <Navigate to={redirect} replace/>;
    }

    const role = getUserRole(session);
    const path = location.pathname;

    // Cajero: only cash register
    if (role === 'cajero') {
        if (path !== '/ventas/caja') {
            return <Navigate to="/ventas/caja" replace />;
        }
        return <MinimalLayout />;
    }

    // Reponedor / Default: only restock page
    if (role === 'reponedor' || role === 'default') {
        if (path !== '/productos/reposiciones') {
            return <Navigate to="/productos/reposiciones" replace />;
        }
        return <MinimalLayout />;
    }

    // Encargado: everything except cash register
    if (role === 'encargado' && path === '/ventas/caja') {
        return <Navigate to="/" replace />;
    }

    // Admin & Encargado: full dashboard layout
    return (
        <DashboardLayout>
            <PageContainer breadcrumbs={[]}>
                <Outlet />
            </PageContainer>
        </DashboardLayout>
    );
};