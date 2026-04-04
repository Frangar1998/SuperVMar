import { useEffect, useState, useMemo, useCallback } from "react";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Dialog from "@mui/material/Dialog";
import DialogContent from "@mui/material/DialogContent";
import DialogTitle from "@mui/material/DialogTitle";
import IconButton from "@mui/material/IconButton";
import MapIcon from "@mui/icons-material/Map";
import CloseIcon from "@mui/icons-material/Close";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import { AllocationService, type ProductAllocation } from "../../services/AllocationService.ts";
import { SupermarketService } from "../../../supermarket/services/SupermarketService.ts";
import type { ZoneFormData } from "../../../supermarket/types/SupermarketTypes.ts";
import type { FilterMode } from "../../types/RestockTypes.ts";
import { buildZoneIssues, buildZoneStatusMap, filterZoneIssues } from "../../services/RestockService.ts";
import { RestockSummaryBanner } from "../../components/Restock/RestockSummaryBanner.tsx";
import { RestockFilterToggle } from "../../components/Restock/RestockFilterToggle.tsx";
import { RestockZoneMap } from "../../components/Restock/RestockZoneMap.tsx";
import { RestockZoneAccordionList } from "../../components/Restock/RestockZoneAccordionList.tsx";

export const RestockPage = () => {
    const { session } = useSession();
    const [loading, setLoading] = useState(true);
    const [allocations, setAllocations] = useState<ProductAllocation[]>([]);
    const [zones, setZones] = useState<ZoneFormData[]>([]);
    const [filter, setFilter] = useState<FilterMode>("all");
    const [expandedZoneId, setExpandedZoneId] = useState<string | null>(null);
    const [mapDialogOpen, setMapDialogOpen] = useState(false);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [allocs, supermarkets] = await Promise.all([
                    AllocationService.getAllocations(session),
                    SupermarketService.getSupermarkets(session),
                ]);
                setAllocations(allocs);
                if (supermarkets.length > 0) {
                    setZones(
                        supermarkets[0].zones?.map((z) => ({
                            id: z.id,
                            name: z.name,
                            cornerTopLeft: z.cornerTopLeft,
                            cornerTopRight: z.cornerTopRight,
                            cornerBottomRight: z.cornerBottomRight,
                            cornerBottomLeft: z.cornerBottomLeft,
                            spaces:
                                z.spaces?.map((sp) => ({
                                    id: sp.id,
                                    position: sp.position,
                                    maxSpots: sp.maxSpots,
                                })) ?? [],
                        })) ?? []
                    );
                }
            } catch (error) {
                console.error("Error al cargar datos de reposición:", error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, [session]);

    const zoneIssues = useMemo(() => buildZoneIssues(allocations), [allocations]);
    const zoneStatusMap = useMemo(() => buildZoneStatusMap(allocations), [allocations]);
    const filteredIssues = useMemo(() => filterZoneIssues(zoneIssues, filter), [zoneIssues, filter]);

    const totalEmpty = useMemo(() => allocations.filter((a) => a.quantity === 0).length, [allocations]);
    const totalLow = useMemo(() => allocations.filter((a) => a.quantity > 0 && a.quantity < 3).length, [allocations]);

    const handleZoneClick = useCallback((zoneId: string) => {
        setExpandedZoneId((prev) => (prev === zoneId ? null : zoneId));
    }, []);

    const handleMapDialogZoneClick = useCallback((zoneId: string) => {
        setExpandedZoneId((prev) => (prev === zoneId ? null : zoneId));
        setMapDialogOpen(false);
    }, []);

    const handleRestock = useCallback(
        async (spaceId: string, productId: string, newQuantity: number) => {
            await AllocationService.assignProduct(spaceId, productId, newQuantity, session);
            setAllocations((prev) =>
                prev.map((a) => (a.space.id === spaceId ? { ...a, quantity: newQuantity } : a))
            );
        },
        [session]
    );

    if (loading) return <LoadingComponent />;

    return (
        <Box sx={{ p: { xs: 1, sm: 2, md: 3 }, maxWidth: 1400, mx: "auto" }}>

            <RestockSummaryBanner totalEmpty={totalEmpty} totalLow={totalLow} />

            <Box sx={{ display: "flex", gap: 2, alignItems: "center", flexWrap: "wrap", mb: { xs: 1, md: 0 } }}>
                <RestockFilterToggle value={filter} onChange={setFilter} />
                <Button
                    variant="outlined"
                    startIcon={<MapIcon />}
                    size="small"
                    onClick={() => setMapDialogOpen(true)}
                    sx={{ display: { xs: "inline-flex", md: "none" }, mb: 2 }}
                >
                    Ver mapa
                </Button>
            </Box>

            {/* Map Dialog for mobile */}
            <Dialog
                open={mapDialogOpen}
                onClose={() => setMapDialogOpen(false)}
                maxWidth="md"
                fullWidth
                PaperProps={{ sx: { m: 1, maxHeight: "90vh" } }}
            >
                <DialogTitle sx={{ display: "flex", alignItems: "center", justifyContent: "space-between", py: 1.5 }}>
                    Mapa del supermercado
                    <IconButton onClick={() => setMapDialogOpen(false)} size="small">
                        <CloseIcon />
                    </IconButton>
                </DialogTitle>
                <DialogContent sx={{ px: 1, pb: 2, overflow: "auto" }}>
                    <RestockZoneMap
                        zones={zones}
                        zoneStatusMap={zoneStatusMap}
                        selectedZoneId={expandedZoneId}
                        onZoneClick={handleMapDialogZoneClick}
                    />
                </DialogContent>
            </Dialog>

            <Box sx={{ display: "flex", gap: 3, alignItems: "flex-start" }}>
                {/* Inline map — desktop only */}
                <Box sx={{ display: { xs: "none", md: "block" } }}>
                    <RestockZoneMap
                        zones={zones}
                        zoneStatusMap={zoneStatusMap}
                        selectedZoneId={expandedZoneId}
                        onZoneClick={handleZoneClick}
                    />
                </Box>

                <Box sx={{ flex: 1, minWidth: 0 }}>
                    <RestockZoneAccordionList
                        zones={filteredIssues}
                        expandedZoneId={expandedZoneId}
                        onZoneClick={handleZoneClick}
                        onRestock={handleRestock}
                    />
                </Box>
            </Box>
        </Box>
    );
};
