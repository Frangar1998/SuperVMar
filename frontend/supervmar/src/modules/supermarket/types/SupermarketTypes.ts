export interface Address {
    id: string;
    name: string;
    number: string;
    postalCode: string;
    city: string;
    province: string;
}

export interface Space {
    id: string;
    position: { x: number; y: number; z: number };
    maxSpots: number;
}

export interface Zone {
    id: string;
    name: string;
    cornerTopLeft: { x: number; y: number };
    cornerTopRight: { x: number; y: number };
    cornerBottomRight: { x: number; y: number };
    cornerBottomLeft: { x: number; y: number };
    spaces: Space[];
}

export interface Supermarket {
    id: string;
    name: string;
    address: Address;
    phone: string;
    email: string;
    zones: Zone[];
}

export interface SupermarketFormData {
    name: string;
    phone: string;
    email: string;
    address: Address;
    zones: ZoneFormData[];
}

export interface ZoneFormData {
    id: string;
    name: string;
    cornerTopLeft: { x: number; y: number };
    cornerTopRight: { x: number; y: number };
    cornerBottomRight: { x: number; y: number };
    cornerBottomLeft: { x: number; y: number };
    spaces: SpaceFormData[];
}

export interface SpaceFormData {
    id: string;
    position: { x: number; y: number; z: number };
    maxSpots: number;
}

export interface SelectionRect {
    startX: number;
    startY: number;
    endX: number;
    endY: number;
}

export const MAP_CONFIG = {
    width: 600,
    height: 500,
    cellSize: 10,
    cols: 54,
    rows: 44,
    padding: 30,
} as const;

export const SHELF_DIRECTIONS = [
    { y: 0, label: 'Norte' },
    { y: 1, label: 'Sur' },
    { y: 2, label: 'Este' },
    { y: 3, label: 'Oeste' },
] as const;

export const MAX_SHELVES = SHELF_DIRECTIONS.length;

export const directionLabel = (y: number): string =>
    SHELF_DIRECTIONS.find(d => d.y === y)?.label ?? `Dirección ${y}`;


