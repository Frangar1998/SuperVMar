import AppBar from "@mui/material/AppBar";
import Toolbar from "@mui/material/Toolbar";
import Typography from "@mui/material/Typography";
import Button from "@mui/material/Button";
import Box from "@mui/material/Box";
import LogoutIcon from "@mui/icons-material/Logout";
import { Outlet } from "react-router";
import { useEffect } from "react";
import { useSession } from "../../login/contexts/SessionContext.ts";
import { PushNotificationService } from "../../notification/services/PushNotificationService.ts";

export const MinimalLayout = () => {
    const { session, logout } = useSession();

    useEffect(() => {
        PushNotificationService.init(session);
    }, [session]);

    return (
        <Box sx={{ display: "flex", flexDirection: "column", minHeight: "100vh" }}>
            <AppBar position="static" color="default" elevation={1}>
                <Toolbar variant="dense" sx={{ gap: 1 }}>
                    <img
                        src="/images/supervmar-logo.png"
                        alt="SuperVMar"
                        style={{ height: 28 }}
                    />
                    <Typography variant="subtitle1" sx={{ fontWeight: "bold", flexGrow: 1 }}>
                        SuperVMar
                    </Typography>
                    <Typography variant="body2" color="text.secondary">
                        {session?.username}
                    </Typography>
                    <Button
                        color="inherit"
                        startIcon={<LogoutIcon />}
                        onClick={logout}
                        size="small"
                        sx={{ textTransform: "none" }}
                    >
                        Cerrar sesión
                    </Button>
                </Toolbar>
            </AppBar>
            <Box sx={{ flex: 1, overflow: "auto" }}>
                <Outlet />
            </Box>
        </Box>
    );
};
