import { useRef, useState } from "react";
import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Chip from "@mui/material/Chip";
import List from "@mui/material/List";
import ListItem from "@mui/material/ListItem";
import ListItemAvatar from "@mui/material/ListItemAvatar";
import Avatar from "@mui/material/Avatar";
import Accordion from "@mui/material/Accordion";
import AccordionSummary from "@mui/material/AccordionSummary";
import AccordionDetails from "@mui/material/AccordionDetails";
import Paper from "@mui/material/Paper";
import Button from "@mui/material/Button";
import TextField from "@mui/material/TextField";
import CircularProgress from "@mui/material/CircularProgress";
import ExpandMoreIcon from "@mui/icons-material/ExpandMore";
import CheckCircleIcon from "@mui/icons-material/CheckCircle";
import { directionLabel } from "../../../supermarket/types/SupermarketTypes.ts";
import type { ZoneIssue } from "../../types/RestockTypes.ts";
import { STATUS_COLORS } from "../../types/RestockTypes.ts";
import type { ProductAllocation } from "../../services/AllocationService.ts";

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

interface RestockZoneAccordionListProps {
    zones: ZoneIssue[];
    expandedZoneId: string | null;
    onZoneClick: (zoneId: string) => void;
    onRestock: (spaceId: string, productId: string, newQuantity: number) => Promise<void>;
}

const SpaceRestockItem = ({
    alloc,
    onRestock,
}: {
    alloc: ProductAllocation;
    onRestock: (spaceId: string, productId: string, newQuantity: number) => Promise<void>;
}) => {
    const [saving, setSaving] = useState(false);
    const [customQty, setCustomQty] = useState("");
    const isEmpty = alloc.quantity === 0;
    const quantityColor = isEmpty ? "#F44336" : "#FF9800";

    const handleFullRestock = async () => {
        setSaving(true);
        try {
            await onRestock(alloc.space.id, alloc.product.id, alloc.space.maxSpots);
        } finally {
            setSaving(false);
        }
    };

    const handlePartialRestock = async () => {
        const qty = parseInt(customQty, 10);
        if (isNaN(qty) || qty <= alloc.quantity || qty > alloc.space.maxSpots) return;
        setSaving(true);
        try {
            await onRestock(alloc.space.id, alloc.product.id, qty);
            setCustomQty("");
        } finally {
            setSaving(false);
        }
    };

    return (
        <ListItem
            sx={{
                borderBottom: "1px solid",
                borderColor: "divider",
                flexDirection: "column",
                alignItems: "stretch",
                px: { xs: 1, md: 2 },
                py: 1.5,
            }}
        >
            {/* Product info row */}
            <Box sx={{ display: "flex", gap: 1, alignItems: "flex-start", width: "100%" }}>
                <ListItemAvatar sx={{ minWidth: { xs: 48, md: 56 } }}>
                    <Avatar
                        variant="rounded"
                        src={
                            alloc.product.image
                                ? `${API_BASE_URL}/images/${alloc.product.image}`
                                : undefined
                        }
                        sx={{
                            width: { xs: 40, md: 48 },
                            height: { xs: 40, md: 48 },
                            bgcolor: "grey.200",
                        }}
                    >
                        {!alloc.product.image ? alloc.product.name.charAt(0) : null}
                    </Avatar>
                </ListItemAvatar>
                <Box sx={{ flex: 1, minWidth: 0 }}>
                    <Typography
                        sx={{
                            fontWeight: "bold",
                            fontSize: { xs: "0.85rem", md: "0.95rem" },
                            wordBreak: "break-word",
                        }}
                    >
                        {alloc.product.name}
                    </Typography>
                    <Typography
                        variant="body2"
                        color="text.secondary"
                        sx={{ fontSize: { xs: "0.75rem", md: "0.85rem" } }}
                    >
                        Posición {alloc.space.position.x}, Estantería:{" "}
                        {directionLabel(alloc.space.position.y)}, Altura:{" "}
                        {alloc.space.position.z + 1}
                    </Typography>
                </Box>
                <Chip
                    label={`${alloc.quantity} / ${alloc.space.maxSpots}`}
                    size="small"
                    sx={{
                        bgcolor: quantityColor,
                        color: "#fff",
                        fontWeight: "bold",
                        fontSize: { xs: "0.75rem", md: "0.8rem" },
                        alignSelf: "center",
                    }}
                />
            </Box>

            {/* Restock actions row */}
            <Box
                sx={{
                    display: "flex",
                    gap: 1,
                    mt: 1,
                    alignItems: "center",
                    flexWrap: "wrap",
                }}
            >
                <Button
                    variant="contained"
                    size="small"
                    color="success"
                    startIcon={saving ? <CircularProgress size={16} color="inherit" /> : <CheckCircleIcon />}
                    disabled={saving}
                    onClick={handleFullRestock}
                    sx={{ fontSize: { xs: "0.7rem", md: "0.8rem" }, minWidth: 0 }}
                >
                    Reponer todo
                </Button>
                <TextField
                    type="number"
                    size="small"
                    placeholder="Cantidad"
                    value={customQty}
                    onChange={(e) => setCustomQty(e.target.value)}
                    disabled={saving}
                    slotProps={{
                        htmlInput: {
                            min: alloc.quantity + 1,
                            max: alloc.space.maxSpots,
                        },
                    }}
                    sx={{
                        width: { xs: 85, md: 100 },
                        "& .MuiInputBase-input": { py: 0.7, fontSize: "0.85rem" },
                    }}
                />
                <Button
                    variant="outlined"
                    size="small"
                    disabled={saving || !customQty || parseInt(customQty, 10) <= alloc.quantity || parseInt(customQty, 10) > alloc.space.maxSpots}
                    onClick={handlePartialRestock}
                    sx={{ fontSize: { xs: "0.7rem", md: "0.8rem" }, minWidth: 0 }}
                >
                    Reponer
                </Button>
            </Box>
        </ListItem>
    );
};

export const RestockZoneAccordionList = ({
    zones,
    expandedZoneId,
    onZoneClick,
    onRestock,
}: RestockZoneAccordionListProps) => {
    const zoneRefs = useRef<Map<string, HTMLDivElement>>(new Map());

    const handleChange = (zoneId: string) => {
        onZoneClick(zoneId);
        const el = zoneRefs.current.get(zoneId);
        if (el) {
            setTimeout(() => el.scrollIntoView({ behavior: "smooth", block: "start" }), 150);
        }
    };

    if (zones.length === 0) {
        return (
            <Paper sx={{ p: 3, textAlign: "center" }}>
                <Typography color="text.secondary">
                    No hay zonas con incidencias para el filtro seleccionado.
                </Typography>
            </Paper>
        );
    }

    return (
        <>
            {zones.map((zone) => (
                <Accordion
                    key={zone.zoneId}
                    expanded={expandedZoneId === zone.zoneId}
                    onChange={() => handleChange(zone.zoneId)}
                    ref={(el: HTMLDivElement | null) => {
                        if (el) zoneRefs.current.set(zone.zoneId, el);
                    }}
                    sx={{ mb: 1 }}
                >
                    <AccordionSummary
                        expandIcon={<ExpandMoreIcon />}
                        sx={{
                            borderLeft: `4px solid ${STATUS_COLORS[zone.status]}`,
                            "& .MuiAccordionSummary-content": {
                                alignItems: "center",
                                gap: 1,
                                flexWrap: "wrap",
                            },
                        }}
                    >
                        <Typography sx={{ fontWeight: "bold", fontSize: { xs: "0.9rem", md: "1rem" } }}>
                            {zone.zoneName}
                        </Typography>
                        <Chip
                            label={
                                zone.status === "critical"
                                    ? "Espacios vacíos"
                                    : "Stock bajo"
                            }
                            size="small"
                            sx={{
                                bgcolor: STATUS_COLORS[zone.status],
                                color: "#fff",
                                fontWeight: 600,
                                fontSize: "0.7rem",
                            }}
                        />
                        <Typography
                            variant="body2"
                            color="text.secondary"
                            sx={{ ml: "auto" }}
                        >
                            {zone.spaces.length}{" "}
                            {zone.spaces.length === 1 ? "espacio" : "espacios"}
                        </Typography>
                    </AccordionSummary>
                    <AccordionDetails sx={{ p: { xs: 0.5, md: 2 } }}>
                        <List disablePadding>
                            {zone.spaces.map((alloc) => (
                                <SpaceRestockItem
                                    key={alloc.space.id}
                                    alloc={alloc}
                                    onRestock={onRestock}
                                />
                            ))}
                        </List>
                    </AccordionDetails>
                </Accordion>
            ))}
        </>
    );
};
