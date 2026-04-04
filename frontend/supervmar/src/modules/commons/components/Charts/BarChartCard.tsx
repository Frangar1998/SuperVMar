import Paper from "@mui/material/Paper";
import Typography from "@mui/material/Typography";
import { BarChart } from "@mui/x-charts/BarChart";

interface BarChartCardProps {
    title: string;
    dataset: Record<string, string | number>[];
    xDataKey: string;
    series: { dataKey: string; label: string; color?: string }[];
    height?: number;
    emptyMessage?: string;
}

export const BarChartCard = ({
    title,
    dataset,
    xDataKey,
    series,
    height = 300,
    emptyMessage = "Sin datos disponibles",
}: BarChartCardProps) => (
    <Paper sx={{ p: 2 }} elevation={2}>
        <Typography variant="h6" gutterBottom>
            {title}
        </Typography>
        {dataset.length > 0 ? (
            <BarChart
                dataset={dataset}
                xAxis={[{ scaleType: "band", dataKey: xDataKey }]}
                series={series}
                height={height}
            />
        ) : (
            <Typography variant="body2" color="text.secondary" sx={{ py: 8, textAlign: "center" }}>
                {emptyMessage}
            </Typography>
        )}
    </Paper>
);
