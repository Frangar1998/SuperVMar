import { Rect } from "react-konva";
import type { SelectionRect } from "../../types/SupermarketTypes.ts";
import { MAP_CONFIG } from "../../types/SupermarketTypes.ts";

const { height, cellSize, padding } = MAP_CONFIG;

interface MapSelectionLayerProps {
    selection: SelectionRect | null;
}

const gridToPixelX = (gx: number) => padding + gx * cellSize;
const gridToPixelY = (gy: number) => height - padding - gy * cellSize;

export const MapSelectionLayer = ({ selection }: MapSelectionLayerProps) => {
    if (!selection) return null;

    const x1 = Math.min(selection.startX, selection.endX);
    const x2 = Math.max(selection.startX, selection.endX);
    const y1 = Math.min(selection.startY, selection.endY);
    const y2 = Math.max(selection.startY, selection.endY);

    const pixelX = gridToPixelX(x1);
    const pixelY = gridToPixelY(y2);
    const w = (x2 - x1) * cellSize;
    const h = (y2 - y1) * cellSize;

    if (w === 0 || h === 0) return null;

    return (
        <Rect
            x={pixelX}
            y={pixelY}
            width={w}
            height={h}
            fill="#2196F3"
            opacity={0.2}
            stroke="#2196F3"
            strokeWidth={2}
            dash={[6, 3]}
            cornerRadius={2}
            listening={false}
        />
    );
};

