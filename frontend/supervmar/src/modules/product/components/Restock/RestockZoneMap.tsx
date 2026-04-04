import { useState, useEffect, useRef } from "react";
import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Paper from "@mui/material/Paper";
import { Stage, Layer, Group, Rect, Text, Image as KonvaImage } from "react-konva";
import { MAP_CONFIG } from "../../../supermarket/types/SupermarketTypes.ts";
import type { ZoneFormData } from "../../../supermarket/types/SupermarketTypes.ts";
import type { ZoneStatus } from "../../types/RestockTypes.ts";
import { STATUS_COLORS } from "../../types/RestockTypes.ts";

const gridToPixelX = (gx: number) => MAP_CONFIG.padding + gx * MAP_CONFIG.cellSize;
const gridToPixelY = (gy: number) => MAP_CONFIG.height - MAP_CONFIG.padding - gy * MAP_CONFIG.cellSize;

interface RestockZoneMapProps {
    zones: ZoneFormData[];
    zoneStatusMap: Map<string, ZoneStatus>;
    selectedZoneId: string | null;
    onZoneClick: (zoneId: string) => void;
}

export const RestockZoneMap = ({ zones, zoneStatusMap, selectedZoneId, onZoneClick }: RestockZoneMapProps) => {
    const [bgImage, setBgImage] = useState<HTMLImageElement | null>(null);
    const containerRef = useRef<HTMLDivElement>(null);
    const [containerWidth, setContainerWidth] = useState<number>(MAP_CONFIG.width);

    useEffect(() => {
        const img = new window.Image();
        img.src = "/images/supermarket-layout.svg";
        img.onload = () => setBgImage(img);
    }, []);

    useEffect(() => {
        const el = containerRef.current;
        if (!el) return;

        const observer = new ResizeObserver((entries) => {
            const width = entries[0]?.contentRect.width;
            if (width && width > 0) setContainerWidth(width);
        });
        observer.observe(el);
        return () => observer.disconnect();
    }, []);

    const scale = Math.min(1, containerWidth / MAP_CONFIG.width);
    const scaledHeight = MAP_CONFIG.height * scale;
    const baseFontSize = 11;
    const fontSize = scale < 1 ? baseFontSize / scale : baseFontSize;

    return (
        <Box sx={{ flexShrink: 0 }}>
            <Paper sx={{ p: 2 }} elevation={2}>
                <Typography variant="subtitle2" gutterBottom>
                    Mapa de zonas
                </Typography>
                <Box ref={containerRef} sx={{ width: "100%" }}>
                    <Stage
                        width={MAP_CONFIG.width * scale}
                        height={scaledHeight}
                        scaleX={scale}
                        scaleY={scale}
                    >
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
                                const w =
                                    (zone.cornerTopRight.x - zone.cornerTopLeft.x) *
                                    MAP_CONFIG.cellSize;
                                const h =
                                    (zone.cornerTopLeft.y - zone.cornerBottomLeft.y) *
                                    MAP_CONFIG.cellSize;
                                const status = zoneStatusMap.get(zone.id) ?? "ok";
                                const color = STATUS_COLORS[status];
                                const isSelected = selectedZoneId === zone.id;

                                return (
                                    <Group key={zone.id}>
                                        <Rect
                                            x={x}
                                            y={y}
                                            width={w}
                                            height={h}
                                            fill={color}
                                            opacity={isSelected ? 0.7 : 0.4}
                                            stroke={isSelected ? "#000" : color}
                                            strokeWidth={isSelected ? 2.5 : 1.5}
                                            cornerRadius={2}
                                            onClick={() => onZoneClick(zone.id)}
                                            onTap={() => onZoneClick(zone.id)}
                                        />
                                        <Text
                                            x={x}
                                            y={y}
                                            width={w}
                                            height={h}
                                            text={zone.name}
                                            fontSize={fontSize}
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
                <Box
                    sx={{
                        display: "flex",
                        gap: 2,
                        mt: 1.5,
                        justifyContent: "center",
                        flexWrap: "wrap",
                    }}
                >
                    {[
                        { key: "critical" as const, label: "Vacío" },
                        { key: "warning" as const, label: "Stock bajo" },
                        { key: "ok" as const, label: "OK" },
                    ].map(({ key, label }) => (
                        <Box
                            key={key}
                            sx={{ display: "flex", alignItems: "center", gap: 0.5 }}
                        >
                            <Box
                                sx={{
                                    width: 12,
                                    height: 12,
                                    borderRadius: "50%",
                                    bgcolor: STATUS_COLORS[key],
                                }}
                            />
                            <Typography variant="caption">{label}</Typography>
                        </Box>
                    ))}
                </Box>
            </Paper>
        </Box>
    );
};
