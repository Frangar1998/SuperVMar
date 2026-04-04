import { useState, useEffect, useMemo } from "react";
import Box from "@mui/material/Box";
import Grid from "@mui/material/Grid";
import ShoppingCartIcon from "@mui/icons-material/ShoppingCart";
import EuroIcon from "@mui/icons-material/Euro";
import PaymentsIcon from "@mui/icons-material/Payments";
import CreditCardIcon from "@mui/icons-material/CreditCard";
import { useSession } from "../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../commons/components/LoadingComponent.tsx";
import { KpiCard } from "../../commons/components/Charts/KpiCard.tsx";
import { BarChartCard } from "../../commons/components/Charts/BarChartCard.tsx";
import { PieChartCard } from "../../commons/components/Charts/PieChartCard.tsx";
import { DateFilterComponent } from "../components/DateFilterComponent.tsx";
import { RestockMapComponent } from "../components/RestockMapComponent.tsx";
import { ZoneIssuesTable } from "../components/ZoneIssuesTable.tsx";
import { DashboardService } from "../services/DashboardService.ts";
import { computeKPIs, computeDailySales, computePayMethodData, computeZoneRestockInfo } from "../services/DashboardService.ts";
import type { DateFilter } from "../types/DashboardTypes.ts";
import type { ZoneFormData } from "../../supermarket/types/SupermarketTypes.ts";
import { SupermarketService } from "../../supermarket/services/SupermarketService.ts";

const formatCurrency = (value: number): string =>
    new Intl.NumberFormat("es-ES", { style: "currency", currency: "EUR" }).format(value);

export const HomePage = () => {
    const { session } = useSession();
    const [loading, setLoading] = useState(true);
    const [sales, setSales] = useState<any[]>([]);
    const [allocations, setAllocations] = useState<any[]>([]);
    const [zones, setZones] = useState<ZoneFormData[]>([]);
    const [dateFilter, setDateFilter] = useState<DateFilter>("month");
    const [customDateFrom, setCustomDateFrom] = useState<string>("");
    const [customDateTo, setCustomDateTo] = useState<string>("");

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

    useEffect(() => {
        const fetchSales = async () => {
            setLoading(true);
            try {
                const data = await DashboardService.fetchSalesData(session, dateFilter, customDateFrom, customDateTo);
                setSales(data);
            } catch {
                setSales([]);
            } finally {
                setLoading(false);
            }
        };
        fetchSales();
    }, [session, dateFilter, customDateFrom, customDateTo]);

    const kpi = useMemo(() => computeKPIs(sales), [sales]);
    const dailySales = useMemo(() => computeDailySales(sales), [sales]);
    const payMethodData = useMemo(() => computePayMethodData(sales), [sales]);
    const zoneRestockInfo = useMemo(() => computeZoneRestockInfo(allocations), [allocations]);

    const barDataset = useMemo(
        () => dailySales.map((d) => ({ date: d.date, revenue: d.revenue })),
        [dailySales],
    );

    const pieData = useMemo(
        () => payMethodData.map((p, i) => ({
            id: i,
            value: p.count,
            label: p.method === "cash" ? "Efectivo" : "Tarjeta",
        })),
        [payMethodData],
    );

    if (loading && sales.length === 0) return <LoadingComponent />;

    return (
        <Box sx={{ p: 3 }}>
            <DateFilterComponent
                value={dateFilter}
                customDateFrom={customDateFrom}
                customDateTo={customDateTo}
                onChange={setDateFilter}
                onCustomDateFromChange={setCustomDateFrom}
                onCustomDateToChange={setCustomDateTo}
            />

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
                    <BarChartCard
                        title="Ventas por día"
                        dataset={barDataset}
                        xDataKey="date"
                        series={[{ dataKey: "revenue", label: "Ingresos (€)", color: "#1976d2" }]}
                        emptyMessage="Sin datos para el período seleccionado"
                    />
                </Grid>
                <Grid size={{ xs: 12, md: 5 }}>
                    <PieChartCard
                        title="Método de pago"
                        data={pieData}
                        emptyMessage="Sin datos para el período seleccionado"
                    />
                </Grid>
            </Grid>

            <RestockMapComponent zones={zones} zoneRestockInfo={zoneRestockInfo} />

            <ZoneIssuesTable zones={zoneRestockInfo} />
        </Box>
    );
};