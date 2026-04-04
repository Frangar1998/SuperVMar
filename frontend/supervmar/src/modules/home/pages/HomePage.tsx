import { useState, useEffect, useMemo, useRef } from "react";
import Box from "@mui/material/Box";
import Grid from "@mui/material/Grid";
import Paper from "@mui/material/Paper";
import Typography from "@mui/material/Typography";
import ToggleButton from "@mui/material/ToggleButton";
import ToggleButtonGroup from "@mui/material/ToggleButtonGroup";
import TextField from "@mui/material/TextField";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableContainer from "@mui/material/TableContainer";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import ShoppingCartIcon from "@mui/icons-material/ShoppingCart";
import EuroIcon from "@mui/icons-material/Euro";
import PaymentsIcon from "@mui/icons-material/Payments";
import CreditCardIcon from "@mui/icons-material/CreditCard";
import { BarChart } from "@mui/x-charts/BarChart";
import { PieChart } from "@mui/x-charts/PieChart";
import { Stage, Layer, Group, Rect, Text, Image as KonvaImage } from "react-konva";

import { useSession } from "../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../commons/components/LoadingComponent.tsx";
import { DashboardService } from "../services/DashboardService.ts";
import { computeKPIs, computeDailySales, computePayMethodData, computeZoneRestockInfo } from "../services/DashboardService.ts";
import type { DateFilter } from "../types/DashboardTypes.ts";
import { MAP_CONFIG } from "../../supermarket/types/SupermarketTypes.ts";
import type { ZoneFormData } from "../../supermarket/types/SupermarketTypes.ts";
import { SupermarketService } from "../../supermarket/services/SupermarketService.ts";

const gridToPixelX = (gx: number) => MAP_CONFIG.padding + gx * MAP_CONFIG.cellSize;
const gridToPixelY = (gy: number) => MAP_CONFIG.height - MAP_CONFIG.padding - gy * MAP_CONFIG.cellSize;

const STATUS_COLORS: Record<string, string> = {
    ok: "#4CAF50",
    warning: "#FF9800",
    critical: "#F44336",
};

const formatCurrency = (value: number): string =>
    new Intl.NumberFormat("es-ES", { style: "currency", currency: "EUR" }).format(value);

interface KpiCardProps {
    title: string;
    value: string;
    icon: React.ReactNode;
    color: string;
}

const KpiCard = ({ title, value, icon, color }: KpiCardProps) => (
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

export const HomePage = () => {
    const { session } = useSession();
    const [loading, setLoading] = useState(true);
    const [sales, setSales] = useState<any[]>([]);
    const [allocations, setAllocations] = useState<any[]>([]);
    const [zones, setZones] = useState<ZoneFormData[]>([]);
    const [dateFilter, setDateFilter] = useState<DateFilter>("month");
    const [customDate, setCustomDate] = useState<string>("");
    const [bgImage, setBgImage] = useState<HTMLImageElement | null>(null);

    const bgImageRef = useRef<HTMLImageElement | null>(null);

    // Fetch allocations and zones once on mount
    useEffect(() => {
        const fetchStatic = async () => {
            try {
                const [allocs, supermarkets] = await Promise.all([
                    DashboardService.fetchAllocations(session),
                    SupermarketService.getSupermarkets(session),
                ]);
                setAllocations(allocs);
                if (supermarkets.length > 0) {
                    setZones(supermarkets[0].zones as ZoneFormData[]);
                }
            } catch (error) {
                console.error(error);
            }
        };
        fetchStatic();
    }, [session]);

    // Load background image
    useEffect(() => {
        const img = new window.Image();
        img.src = "/images/supermarket-layout.svg";
        img.onload = () => {
            bgImageRef.current = img;
            setBgImage(img);
        };
    }, []);

    // Fetch sales whenever dateFilter or customDate changes
    useEffect(() => {
        const fetchSales = async () => {
            setLoading(true);
            try {
                const data = await DashboardService.fetchSalesData(session, dateFilter, customDate);
                setSales(data);
            } catch {
                setSales([]);
            } finally {
                setLoading(false);
            }
        };
        fetchSales();
    }, [session, dateFilter, customDate]);

    const kpi = useMemo(() => computeKPIs(sales), [sales]);
    const dailySales = useMemo(() => computeDailySales(sales), [sales]);
    const payMethodData = useMemo(() => computePayMethodData(sales), [sales]);
    const zoneRestockInfo = useMemo(() => computeZoneRestockInfo(allocations), [allocations]);

    const handleDateFilterChange = (_: React.MouseEvent<HTMLElement>, value: DateFilter | null) => {
        if (value) setDateFilter(value);
    };

    const zonesWithIssues = useMemo(
        () => zoneRestockInfo.filter((z) => z.status !== "ok"),
        [zoneRestockInfo],
    );

    const zoneStatusMap = useMemo(() => {
        const map = new Map<string, string>();
        for (const z of zoneRestockInfo) map.set(z.zoneId, z.status);
        return map;
    }, [zoneRestockInfo]);

    if (loading && sales.length === 0) return <LoadingComponent />;

    return (
        <Box sx={{ p: 3 }}>
            {/* Date Filter */}
            <Box sx={{ display: "flex", alignItems: "center", gap: 2, mb: 3, flexWrap: "wrap" }}>
                <ToggleButtonGroup
                    value={dateFilter}
                    exclusive
                    onChange={handleDateFilterChange}
                    size="small"
                >
                    <ToggleButton value="today">Hoy</ToggleButton>
                    <ToggleButton value="week">7 días</ToggleButton>
                    <ToggleButton value="month">30 días</ToggleButton>
                    <ToggleButton value="year">1 Año</ToggleButton>
                    <ToggleButton value="custom">Personalizado</ToggleButton>
                </ToggleButtonGroup>
                {dateFilter === "custom" && (
                    <TextField
                        type="date"
                        size="small"
                        value={customDate}
                        onChange={(e) => setCustomDate(e.target.value)}
                        slotProps={{ inputLabel: { shrink: true } }}
                    />
                )}
            </Box>

            {/* KPI Cards */}
            <Grid container spacing={3} sx={{ mb: 3 }}>
                <Grid size={{ xs: 12, sm: 6, md: 3 }}>
                    <KpiCard
                        title="Ventas totales"
                        value={String(kpi.totalSales)}
                        icon={<ShoppingCartIcon fontSize="large" />}
                        color="#1976d2"
                    />
                </Grid>
                <Grid size={{ xs: 12, sm: 6, md: 3 }}>
                    <KpiCard
                        title="Ingresos"
                        value={formatCurrency(kpi.totalRevenue)}
                        icon={<EuroIcon fontSize="large" />}
                        color="#2e7d32"
                    />
                </Grid>
                <Grid size={{ xs: 12, sm: 6, md: 3 }}>
                    <KpiCard
                        title="Efectivo"
                        value={formatCurrency(kpi.cashRevenue)}
                        icon={<PaymentsIcon fontSize="large" />}
                        color="#ed6c02"
                    />
                </Grid>
                <Grid size={{ xs: 12, sm: 6, md: 3 }}>
                    <KpiCard
                        title="Tarjeta"
                        value={formatCurrency(kpi.cardRevenue)}
                        icon={<CreditCardIcon fontSize="large" />}
                        color="#9c27b0"
                    />
                </Grid>
            </Grid>

            {/* Charts */}
            <Grid container spacing={3} sx={{ mb: 3 }}>
                <Grid size={{ xs: 12, md: 7 }}>
                    <Paper sx={{ p: 2 }} elevation={2}>
                        <Typography variant="h6" gutterBottom>
                            Ventas por día
                        </Typography>
                        {dailySales.length > 0 ? (
                            <BarChart
                                dataset={dailySales.map((d) => ({ date: d.date, revenue: d.revenue }))}
                                xAxis={[{ scaleType: "band", dataKey: "date" }]}
                                series={[{ dataKey: "revenue", label: "Ingresos (€)", color: "#1976d2" }]}
                                height={300}
                            />
                        ) : (
                            <Typography variant="body2" color="text.secondary" sx={{ py: 8, textAlign: "center" }}>
                                Sin datos para el período seleccionado
                            </Typography>
                        )}
                    </Paper>
                </Grid>
                <Grid size={{ xs: 12, md: 5 }}>
                    <Paper sx={{ p: 2 }} elevation={2}>
                        <Typography variant="h6" gutterBottom>
                            Método de pago
                        </Typography>
                        {payMethodData.length > 0 ? (
                            <PieChart
                                series={[
                                    {
                                        data: payMethodData.map((p, i) => ({
                                            id: i,
                                            value: p.count,
                                            label: p.method === "cash" ? "Efectivo" : "Tarjeta",
                                        })),
                                    },
                                ]}
                                height={300}
                            />
                        ) : (
                            <Typography variant="body2" color="text.secondary" sx={{ py: 8, textAlign: "center" }}>
                                Sin datos para el período seleccionado
                            </Typography>
                        )}
                    </Paper>
                </Grid>
            </Grid>

            {/* Restock Map */}
            <Paper sx={{ p: 2, mb: 3 }} elevation={2}>
                <Typography variant="h6" gutterBottom>
                    Estado de reposición por zona
                </Typography>
                <Box sx={{ display: "flex", justifyContent: "center", overflow: "auto" }}>
                    <Stage width={MAP_CONFIG.width} height={MAP_CONFIG.height}>
                        <Layer>
                            {bgImage && (
                                <KonvaImage
                                    image={bgImage}
                                    x={0}
                                    y={0}
                                    width={MAP_CONFIG.width}
                                    height={MAP_CONFIG.height}
                                />
                            )}
                            {zones.map((zone) => {
                                const x = gridToPixelX(zone.cornerTopLeft.x);
                                const y = gridToPixelY(zone.cornerTopLeft.y);
                                const w = (zone.cornerTopRight.x - zone.cornerTopLeft.x) * MAP_CONFIG.cellSize;
                                const h = (zone.cornerTopLeft.y - zone.cornerBottomLeft.y) * MAP_CONFIG.cellSize;
                                const status = zoneStatusMap.get(zone.id) ?? "ok";
                                const color = STATUS_COLORS[status];

                                return (
                                    <Group key={zone.id}>
                                        <Rect
                                            x={x}
                                            y={y}
                                            width={w}
                                            height={h}
                                            fill={color}
                                            opacity={0.45}
                                            stroke={color}
                                            strokeWidth={1.5}
                                            cornerRadius={2}
                                        />
                                        <Text
                                            x={x}
                                            y={y}
                                            width={w}
                                            height={h}
                                            text={zone.name}
                                            fontSize={11}
                                            fontStyle="bold"
                                            fill="#222"
                                            align="center"
                                            verticalAlign="middle"
                                            listening={false}
                                        />
                                    </Group>
                                );
                            })}
                        </Layer>
                    </Stage>
                </Box>
                {/* Legend */}
                <Box sx={{ display: "flex", gap: 3, mt: 2, justifyContent: "center", flexWrap: "wrap" }}>
                    <Box sx={{ display: "flex", alignItems: "center", gap: 0.5 }}>
                        <Box sx={{ width: 14, height: 14, borderRadius: "50%", bgcolor: STATUS_COLORS.ok }} />
                        <Typography variant="body2">Sin incidencias</Typography>
                    </Box>
                    <Box sx={{ display: "flex", alignItems: "center", gap: 0.5 }}>
                        <Box sx={{ width: 14, height: 14, borderRadius: "50%", bgcolor: STATUS_COLORS.warning }} />
                        <Typography variant="body2">Stock bajo</Typography>
                    </Box>
                    <Box sx={{ display: "flex", alignItems: "center", gap: 0.5 }}>
                        <Box sx={{ width: 14, height: 14, borderRadius: "50%", bgcolor: STATUS_COLORS.critical }} />
                        <Typography variant="body2">Espacios vacíos</Typography>
                    </Box>
                </Box>
            </Paper>

            {/* Zone Issues Table */}
            {zonesWithIssues.length > 0 && (
                <Paper sx={{ p: 2 }} elevation={2}>
                    <Typography variant="h6" gutterBottom>
                        Zonas con incidencias
                    </Typography>
                    <TableContainer>
                        <Table size="small">
                            <TableHead>
                                <TableRow>
                                    <TableCell>Zona</TableCell>
                                    <TableCell align="right">Espacios vacíos</TableCell>
                                    <TableCell align="right">Stock bajo</TableCell>
                                    <TableCell>Estado</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {zonesWithIssues.map((zone) => (
                                    <TableRow key={zone.zoneId}>
                                        <TableCell>{zone.zoneName}</TableCell>
                                        <TableCell align="right">{zone.emptySpaces}</TableCell>
                                        <TableCell align="right">{zone.lowStockSpaces}</TableCell>
                                        <TableCell>
                                            <Box
                                                sx={{
                                                    display: "inline-block",
                                                    width: 12,
                                                    height: 12,
                                                    borderRadius: "50%",
                                                    bgcolor: STATUS_COLORS[zone.status],
                                                    mr: 1,
                                                    verticalAlign: "middle",
                                                }}
                                            />
                                            {zone.status === "critical" ? "Crítico" : "Atención"}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </TableContainer>
                </Paper>
            )}
        </Box>
    );
};