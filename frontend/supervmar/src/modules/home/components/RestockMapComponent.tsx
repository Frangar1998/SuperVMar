import { useEffect, useState } from "react";
import Box from "@mui/material/Box";
import Paper from "@mui/material/Paper";
import Typography from "@mui/material/Typography";
import { Stage, Layer, Group, Rect, Text, Image as KonvaImage } from "react-konva";
import { MAP_CONFIG } from "../../supermarket/types/SupermarketTypes.ts";
import type { ZoneFormData } from "../../supermarket/types/SupermarketTypes.ts";
import type { ZoneRestockInfo } from "../types/DashboardTypes.ts";

const gridToPixelX = (gx: number) => MAP_CONFIG.padding + gx * MAP_CONFIG.cellSize;
const gridToPixelY = (gy: number) => MAP_CONFIG.height - MAP_CONFIG.padding - gy * MAP_CONFIG.cellSize;

const STATUS_COLORS: Record<string, string> = {
    ok: "#4CAF50",
    warning: "#FF9800",
    critical: "#F44336",
};

interface RestockMapProps {
    zones: ZoneFormData[];
    zoneRestockInfo: ZoneRestockInfo[];
}

export const RestockMapComponent = ({ zones, zoneRestockInfo }: RestockMapProps) => {
    const [bgImage, setBgImage] = useState<HTMLImageElement | null>(null);

    useEffect(() => {
        const img = new window.Image();
        img.src = "/images/supermarket-layout.svg";
        img.onload = () => setBgImage(img);
    }, []);

    const zoneStatusMap = new Map(zoneRestockInfo.map((z) => [z.zoneId, z.status]));

    return (
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
                {[
                    { key: "ok", label: "Sin incidencias" },
                    { key: "warning", label: "Stock bajo" },
                    { key: "critical", label: "Espacios vacíos" },
                ].map(({ key, label }) => (
                    <Box key={key} sx={{ display: "flex", alignItems: "center", gap: 0.5 }}>
                        <Box sx={{ width: 14, height: 14, borderRadius: "50%", bgcolor: STATUS_COLORS[key] }} />
                        <Typography variant="body2">{label}</Typography>
                    </Box>
                ))}
            </Box>
        </Paper>
    );
};
