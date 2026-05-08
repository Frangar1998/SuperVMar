import { Group, Line, Text } from "react-konva";
import { MAP_CONFIG } from "../../types/SupermarketTypes.ts";

const { width, height, cellSize, padding, cols, rows } = MAP_CONFIG;

export const MapGridLayer = () => {
    const lines = [];

    // Vertical lines
    for (let i = 0; i <= cols; i++) {
        const x = padding + i * cellSize;
        lines.push(
            <Line
                key={`v-${i}`}
                points={[x, padding, x, height - padding]}
                stroke="#ccc"
                strokeWidth={0.3}
                opacity={i % 5 === 0 ? 0.7 : 0.5}
            />
        );
    }

    // Horizontal lines
    for (let j = 0; j <= rows; j++) {
        const y = padding + j * cellSize;
        lines.push(
            <Line
                key={`h-${j}`}
                points={[padding, y, width - padding, y]}
                stroke="#ccc"
                strokeWidth={0.3}
                opacity={j % 5 === 0 ? 0.7 : 0.5}
            />
        );
    }

    // X
    const xLabels = [];
    for (let i = 0; i <= cols; i += 10) {
        xLabels.push(
            <Text
                key={`xl-${i}`}
                x={padding + i * cellSize - 5}
                y={height - padding + 6}
                text={String(i)}
                fontSize={9}
                fill="#999"
            />
        );
    }

    // Y
    const yLabels = [];
    for (let j = 0; j <= rows; j += 10) {
        yLabels.push(
            <Text
                key={`yl-${j}`}
                x={6}
                y={height - padding - j * cellSize - 5}
                text={String(j)}
                fontSize={9}
                fill="#999"
            />
        );
    }

    return (
        <Group>
            {lines}
            {xLabels}
            {yLabels}
        </Group>
    );
};

