import Box from "@mui/material/Box";
import Paper from "@mui/material/Paper";
import Typography from "@mui/material/Typography";

interface KpiCardProps {
    title: string;
    value: string;
    icon: React.ReactNode;
    color: string;
}

export const KpiCard = ({ title, value, icon, color }: KpiCardProps) => (
    <Paper
        sx={{
            p: 2.5,
            display: "flex",
            alignItems: "center",
            gap: 2,
            borderLeft: `4px solid ${color}`,
        }}
        elevation={2}
    >
        <Box sx={{ color, display: "flex", alignItems: "center" }}>{icon}</Box>
        <Box>
            <Typography variant="body2" color="text.secondary">
                {title}
            </Typography>
            <Typography variant="h5" fontWeight="bold">
                {value}
            </Typography>
        </Box>
    </Paper>
);
