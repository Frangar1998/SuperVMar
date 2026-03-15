import { Group, Rect, Text } from "react-konva";
import type { ZoneFormData } from "../../types/SupermarketTypes.ts";
import { MAP_CONFIG } from "../../types/SupermarketTypes.ts";

const { height, cellSize, padding } = MAP_CONFIG;

const ZONE_COLORS = [
    '#4CAF50',
    '#2196F3',
    '#FF9800',
    '#9C27B0',
    '#F44336',
    '#00BCD4',
    '#FFEB3B',
    '#795548',
];

interface MapZonesLayerProps {
    zones: ZoneFormData[];
    selectedZoneId: string | null;
    onZoneClick: (zone: ZoneFormData) => void;
}

const gridToPixelX = (gx: number) => padding + gx * cellSize;
const gridToPixelY = (gy: number) => height - padding - gy * cellSize;

export const MapZonesLayer = ({ zones, selectedZoneId, onZoneClick }: MapZonesLayerProps) => {
    return (
        <Group>
            {zones.map((zone, index) => {
                const x = gridToPixelX(zone.cornerTopLeft.x);
                const y = gridToPixelY(zone.cornerTopLeft.y);
                const w = (zone.cornerTopRight.x - zone.cornerTopLeft.x) * cellSize;
                const h = (zone.cornerTopLeft.y - zone.cornerBottomLeft.y) * cellSize;
                const color = ZONE_COLORS[index % ZONE_COLORS.length];
                const isSelected = selectedZoneId === zone.id;

                return (
                    <Group key={zone.id}>
                        <Rect
                            x={x}
                            y={y}
                            width={w}
                            height={h}
                            fill={color}
                            opacity={isSelected ? 0.5 : 0.3}
                            stroke={isSelected ? color : '#333'}
                            strokeWidth={isSelected ? 2.5 : 1}
                            cornerRadius={2}
                            onClick={() => onZoneClick(zone)}
                            onTap={() => onZoneClick(zone)}
                        />
                        <Text
                            x={x}
                            y={y}
                            width={w}
                            height={h}
                            text={zone.name}
                            fontSize={12}
                            fontStyle="bold"
                            fill="#222"
                            align="center"
                            verticalAlign="middle"
                            listening={false}
                        />
                    </Group>
                );
            })}
        </Group>
    );
};

