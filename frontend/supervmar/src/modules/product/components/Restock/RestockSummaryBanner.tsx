import Card from "@mui/material/Card";
import CardContent from "@mui/material/CardContent";
import Typography from "@mui/material/Typography";
import Chip from "@mui/material/Chip";

interface RestockSummaryBannerProps {
    totalEmpty: number;
    totalLow: number;
}

export const RestockSummaryBanner = ({ totalEmpty, totalLow }: RestockSummaryBannerProps) => (
    <Card sx={{ mb: 2 }}>
        <CardContent
            sx={{
                display: "flex",
                flexWrap: "wrap",
                gap: 2,
                alignItems: "center",
                py: { xs: 1.5, md: 2 },
                "&:last-child": { pb: { xs: 1.5, md: 2 } },
            }}
        >
            <Typography variant="subtitle1" sx={{ fontWeight: 500, mr: 1 }}>
                Resumen:
            </Typography>
            <Chip
                label={`${totalEmpty} espacios vacíos`}
                sx={{
                    bgcolor: "#F44336",
                    color: "#fff",
                    fontWeight: "bold",
                    fontSize: { xs: "0.8rem", md: "0.875rem" },
                }}
            />
            <Chip
                label={`${totalLow} stock bajo`}
                sx={{
                    bgcolor: "#FF9800",
                    color: "#fff",
                    fontWeight: "bold",
                    fontSize: { xs: "0.8rem", md: "0.875rem" },
                }}
            />
        </CardContent>
    </Card>
);
