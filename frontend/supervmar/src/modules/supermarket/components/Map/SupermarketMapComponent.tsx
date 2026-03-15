import { useEffect, useRef, useState, useCallback } from "react";
import { Stage, Layer, Image as KonvaImage } from "react-konva";
import type Konva from "konva";
import type { ZoneFormData, SelectionRect } from "../../types/SupermarketTypes.ts";
import { MAP_CONFIG } from "../../types/SupermarketTypes.ts";
import { MapGridLayer } from "./MapGridLayer.tsx";
import { MapZonesLayer } from "./MapZonesLayer.tsx";
import { MapSelectionLayer } from "./MapSelectionLayer.tsx";
import { MapToolbar } from "./MapToolbar.tsx";
import { ZoneDialogComponent } from "../ZoneDialogComponent.tsx";
import { ConfirmDialogComponent } from "../../../commons/components/ConfirmDialogComponent.tsx";
import { v7 } from "uuid";
import Box from "@mui/material/Box";
import Paper from "@mui/material/Paper";
import { SpacesPanelComponent } from "../SpacesPanelComponent.tsx";
import type { SpaceFormData } from "../../types/SupermarketTypes.ts";

const { width, height, cellSize, padding } = MAP_CONFIG;

interface SupermarketMapProps {
    zones: ZoneFormData[];
    onZonesChange: (zones: ZoneFormData[]) => void;
}

const pixelToGridX = (px: number): number => Math.round((px - padding) / cellSize);
const pixelToGridY = (py: number): number => Math.round((height - padding - py) / cellSize);

const clampGrid = (val: number, max: number): number => Math.max(0, Math.min(val, max));

export const SupermarketMapComponent = ({ zones, onZonesChange }: SupermarketMapProps) => {
    const stageRef = useRef<Konva.Stage>(null);
    const [bgImage, setBgImage] = useState<HTMLImageElement | null>(null);

    const [isCreating, setIsCreating] = useState(false);
    const [isRepositioning, setIsRepositioning] = useState(false);
    const [isDragging, setIsDragging] = useState(false);
    const [selection, setSelection] = useState<SelectionRect | null>(null);
    const [selectedZoneId, setSelectedZoneId] = useState<string | null>(null);

    const [createDialogOpen, setCreateDialogOpen] = useState(false);
    const [editDialogOpen, setEditDialogOpen] = useState(false);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [pendingSelection, setPendingSelection] = useState<SelectionRect | null>(null);

    useEffect(() => {
        const img = new window.Image();
        img.src = '/images/supermarket-layout.svg';
        img.onload = () => setBgImage(img);
    }, []);

    const selectedZone = zones.find(z => z.id === selectedZoneId) ?? null;

    const handleMouseDown = useCallback((e: Konva.KonvaEventObject<MouseEvent>) => {
        if (!isCreating && !isRepositioning) return;

        const stage = e.target.getStage();
        if (!stage) return;
        const pos = stage.getPointerPosition();
        if (!pos) return;

        const gx = clampGrid(pixelToGridX(pos.x), MAP_CONFIG.cols);
        const gy = clampGrid(pixelToGridY(pos.y), MAP_CONFIG.rows);

        setIsDragging(true);
        setSelection({ startX: gx, startY: gy, endX: gx, endY: gy });
    }, [isCreating, isRepositioning]);

    const handleMouseMove = useCallback((e: Konva.KonvaEventObject<MouseEvent>) => {
        if (!isDragging || !selection) return;

        const stage = e.target.getStage();
        if (!stage) return;
        const pos = stage.getPointerPosition();
        if (!pos) return;

        const gx = clampGrid(pixelToGridX(pos.x), MAP_CONFIG.cols);
        const gy = clampGrid(pixelToGridY(pos.y), MAP_CONFIG.rows);

        setSelection(prev => prev ? { ...prev, endX: gx, endY: gy } : null);
    }, [isDragging, selection]);

    const handleMouseUp = useCallback(() => {
        if (!isDragging || !selection) return;

        setIsDragging(false);

        const x1 = Math.min(selection.startX, selection.endX);
        const x2 = Math.max(selection.startX, selection.endX);
        const y1 = Math.min(selection.startY, selection.endY);
        const y2 = Math.max(selection.startY, selection.endY);

        if (x2 - x1 >= 1 && y2 - y1 >= 1) {
            if (isRepositioning && selectedZoneId) {
                onZonesChange(zones.map(z => z.id === selectedZoneId ? {
                    ...z,
                    cornerTopLeft: { x: x1, y: y2 },
                    cornerTopRight: { x: x2, y: y2 },
                    cornerBottomRight: { x: x2, y: y1 },
                    cornerBottomLeft: { x: x1, y: y1 },
                } : z));
                setIsRepositioning(false);
                setSelection(null);
            } else {
                setPendingSelection({ startX: x1, startY: y1, endX: x2, endY: y2 });
                setCreateDialogOpen(true);
            }
        } else {
            setSelection(null);
        }
    }, [isDragging, selection, isRepositioning, selectedZoneId, zones, onZonesChange]);


    const handleStageClick = useCallback((e: Konva.KonvaEventObject<MouseEvent>) => {
        if (isCreating || isRepositioning) return;
        if (e.target === e.target.getStage()) {
            setSelectedZoneId(null);
        }
    }, [isCreating, isRepositioning]);

    const handleCreateZone = (name: string) => {
        if (!pendingSelection) return;

        const newZone: ZoneFormData = {
            id: v7(),
            name,
            cornerTopLeft: { x: pendingSelection.startX, y: pendingSelection.endY },
            cornerTopRight: { x: pendingSelection.endX, y: pendingSelection.endY },
            cornerBottomRight: { x: pendingSelection.endX, y: pendingSelection.startY },
            cornerBottomLeft: { x: pendingSelection.startX, y: pendingSelection.startY },
            spaces: [],
        };

        onZonesChange([...zones, newZone]);
        setCreateDialogOpen(false);
        setPendingSelection(null);
        setSelection(null);
        setIsCreating(false);
    };

    const handleCancelCreate = () => {
        setCreateDialogOpen(false);
        setPendingSelection(null);
        setSelection(null);
    };

    const handleEditZone = (name: string) => {
        if (!selectedZoneId) return;
        onZonesChange(zones.map(z => z.id === selectedZoneId ? { ...z, name } : z));
        setEditDialogOpen(false);
    };

    const handleDeleteZone = () => {
        if (!selectedZoneId) return;
        onZonesChange(zones.filter(z => z.id !== selectedZoneId));
        setSelectedZoneId(null);
        setDeleteDialogOpen(false);
    };

    const handleZoneClick = (zone: ZoneFormData) => {
        if (isCreating || isRepositioning) return;
        setSelectedZoneId(prev => prev === zone.id ? null : zone.id);
    };

    const handleStartRepositioning = () => {
        setIsRepositioning(true);
        setSelection(null);
    };

    const handleCancelAction = () => {
        setIsCreating(false);
        setIsRepositioning(false);
        setSelection(null);
    };

    const [managingSpacesZoneId, setManagingSpacesZoneId] = useState<string | null>(null);
    const managingSpacesZone = zones.find(z => z.id === managingSpacesZoneId) ?? null;
    const spacesPanelRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (managingSpacesZoneId) {
            const timeout = setTimeout(() => {
                spacesPanelRef.current?.scrollIntoView({ behavior: 'smooth', block: 'end' });
            }, 50);
            return () => clearTimeout(timeout);
        }
    }, [managingSpacesZoneId]);

    const handleSpacesChange = (updatedSpaces: SpaceFormData[]) => {
        if (!managingSpacesZoneId) return;
        onZonesChange(zones.map(z => z.id === managingSpacesZoneId ? { ...z, spaces: updatedSpaces } : z));
    };

    const handleOpenSpaces = () => {
        if (selectedZoneId) {
            setManagingSpacesZoneId(selectedZoneId);
        }
    };

    const handleCloseSpaces = () => {
        setManagingSpacesZoneId(null);
    };

    const cursorStyle = (isCreating || isRepositioning) ? 'crosshair' : 'default';

    return (
        <Box>
            <MapToolbar
                isCreating={isCreating}
                isRepositioning={isRepositioning}
                selectedZone={selectedZone}
                onAddZone={() => { setIsCreating(true); setSelectedZoneId(null); }}
                onEditZone={() => setEditDialogOpen(true)}
                onRepositionZone={handleStartRepositioning}
                onManageSpaces={handleOpenSpaces}
                onDeleteZone={() => setDeleteDialogOpen(true)}
                onCancelCreating={handleCancelAction}
            />

            <Paper
                variant="outlined"
                sx={{
                    display: 'inline-block',
                    cursor: cursorStyle,
                    userSelect: 'none',
                    lineHeight: 0,
                }}
            >
                <Stage
                    ref={stageRef}
                    width={width}
                    height={height}
                    onMouseDown={handleMouseDown}
                    onMouseMove={handleMouseMove}
                    onMouseUp={handleMouseUp}
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

                    <Layer listening={false}>
                        <MapSelectionLayer selection={selection} />
                    </Layer>
                </Stage>
            </Paper>

            <ZoneDialogComponent
                open={createDialogOpen}
                title="Nueva zona"
                onConfirm={handleCreateZone}
                onCancel={handleCancelCreate}
            />

            <ZoneDialogComponent
                open={editDialogOpen}
                title="Editar zona"
                initialName={selectedZone?.name ?? ''}
                onConfirm={handleEditZone}
                onCancel={() => setEditDialogOpen(false)}
            />

            <ConfirmDialogComponent
                open={deleteDialogOpen}
                title="Eliminar zona"
                message={`¿Estás seguro de que deseas eliminar la zona "${selectedZone?.name}"?`}
                confirmText="Eliminar"
                cancelText="Cancelar"
                confirmColor="error"
                onConfirm={handleDeleteZone}
                onCancel={() => setDeleteDialogOpen(false)}
            />

            {managingSpacesZone && (
                <div ref={spacesPanelRef}>
                    <SpacesPanelComponent
                        zone={managingSpacesZone}
                        onSpacesChange={handleSpacesChange}
                        onClose={handleCloseSpaces}
                    />
                </div>
            )}
        </Box>
    );
};

