import Paper from "@mui/material/Paper";
import Typography from "@mui/material/Typography";
import { PieChart } from "@mui/x-charts/PieChart";

interface PieChartDataItem {
    id: number | string;
    value: number;
    label: string;
}

interface PieChartCardProps {
    title: string;
    data: PieChartDataItem[];
    height?: number;
    emptyMessage?: string;
}

export const PieChartCard = ({
    title,
    data,
    height = 300,
    emptyMessage = "Sin datos disponibles",
}: PieChartCardProps) => (
    <Paper sx={{ p: 2 }} elevation={2}>
        <Typography variant="h6" gutterBottom>
            {title}
        </Typography>
        {data.length > 0 ? (
            <PieChart
                series={[{ data }]}
                height={height}
            />
        ) : (
            <Typography variant="body2" color="text.secondary" sx={{ py: 8, textAlign: "center" }}>
                {emptyMessage}
            </Typography>
        )}
    </Paper>
);
