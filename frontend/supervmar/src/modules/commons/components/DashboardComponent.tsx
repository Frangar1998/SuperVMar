import { useSession } from "../../login/contexts/SessionContext.ts";
import { Navigate, useLocation, Outlet } from "react-router";
import { DashboardLayout, PageContainer } from "@toolpad/core";

export const DashboardComponent = () => {
    const { session } = useSession();
    const location = useLocation();

    if (!session) {
        const redirect = `/login?callbackUrl=${encodeURIComponent(location.pathname)}`;

        return <Navigate to={redirect} replace/>;
    }

    return (
        <DashboardLayout>
            <PageContainer
                breadcrumbs={[]}
            >
                <Outlet />
            </PageContainer>
        </DashboardLayout>
    );
};