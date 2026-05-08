import { useEffect, useRef, useState, useCallback } from "react";
import { Stage, Layer, Image as KonvaImage } from "react-konva";
import type Konva from "konva";
import type { ZoneFormData } from "../../../supermarket/types/SupermarketTypes.ts";
import { MAP_CONFIG } from "../../../supermarket/types/SupermarketTypes.ts";
import { MapGridLayer } from "../../../supermarket/components/Map/MapGridLayer.tsx";
import { MapZonesLayer } from "../../../supermarket/components/Map/MapZonesLayer.tsx";
import Box from "@mui/material/Box";
import Paper from "@mui/material/Paper";
import { Chip } from "@mui/material";
import { AllocationSpacesPanelComponent } from "./AllocationSpacesPanelComponent.tsx";
import type { Product, Category } from "../../types/ProductTypes.ts";
import type { ProductAllocation } from "../../services/AllocationService.ts";

const { width, height } = MAP_CONFIG;

interface AllocationMapProps {
    zones: ZoneFormData[];
    allocations: Map<string, ProductAllocation>;
    products: Product[];
    categories: Category[];
    onAssign: (spaceId: string, productId: string, quantity: number) => Promise<void>;
    onRemove: (spaceId: string) => Promise<void>;
}

export const AllocationMapComponent = ({
    zones, allocations, products, categories, onAssign, onRemove
}: AllocationMapProps) => {
    const stageRef = useRef<Konva.Stage>(null);
    const panelRef = useRef<HTMLDivElement>(null);
    const [bgImage, setBgImage] = useState<HTMLImageElement | null>(null);
    const [selectedZoneId, setSelectedZoneId] = useState<string | null>(null);

    useEffect(() => {
        const img = new window.Image();
        img.src = '/images/supermarket-layout.svg';
        img.onload = () => setBgImage(img);
    }, []);

    const selectedZone = zones.find(z => z.id === selectedZoneId) ?? null;

    const handleZoneClick = useCallback((zone: ZoneFormData) => {
        setSelectedZoneId(prev => prev === zone.id ? null : zone.id);
    }, []);

    const handleStageClick = useCallback((e: Konva.KonvaEventObject<MouseEvent>) => {
        if (e.target === e.target.getStage()) {
            setSelectedZoneId(null);
        }
    }, []);

    useEffect(() => {
        if (selectedZoneId) {
            const timeout = setTimeout(() => {
                panelRef.current?.scrollIntoView({ behavior: 'smooth', block: 'end' });
            }, 100);
            return () => clearTimeout(timeout);
        }
    }, [selectedZoneId]);

    return (
        <Box>
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 1 }}>
                {selectedZone ? (
                    <Chip
                        label={`Zona seleccionada: ${selectedZone.name}`}
                        color="primary"
                        variant="filled"
                        sx={{ fontWeight: 'bold' }}
                        onDelete={() => setSelectedZoneId(null)}
                    />
                ) : (
                    <Chip
                        label="Selecciona una zona del mapa para gestionar sus asignaciones"
                        variant="outlined"
                        color="default"
                    />
                )}
            </Box>

            <Paper
                variant="outlined"
                sx={{
                    display: 'inline-block',
                    cursor: 'default',
                    userSelect: 'none',
                    lineHeight: 0,
                }}
            >
                <Stage
                    ref={stageRef}
                    width={width}
                    height={height}
                    onClick={handleStageClick}
                >
                    <Layer>
                        {bgImage && (
                            <KonvaImage
                                image={bgImage}
                                x={0}
                                y={0}
                                width={width}
                                height={height}
                                listening={false}
                            />
                        )}
                    </Layer>

                    <Layer listening={false}>
                        <MapGridLayer />
                    </Layer>

                    <Layer>
                        <MapZonesLayer
                            zones={zones}
                            selectedZoneId={selectedZoneId}
                            onZoneClick={handleZoneClick}
                        />
                    </Layer>
                </Stage>
            </Paper>

            {selectedZone && (
                <div ref={panelRef}>
                    <AllocationSpacesPanelComponent
                        zone={selectedZone}
                        allocations={allocations}
                        products={products}
                        categories={categories}
                        onAssign={onAssign}
                        onRemove={onRemove}
                        onClose={() => setSelectedZoneId(null)}
                    />
                </div>
            )}
        </Box>
    );
};

