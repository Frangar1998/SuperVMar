import type { ProductAllocation } from "./AllocationService.ts";
import type { ZoneStatus, ZoneIssue, FilterMode } from "../types/RestockTypes.ts";

function computeZoneStatus(spaces: ProductAllocation[]): ZoneStatus {
    const hasEmpty = spaces.some((s) => s.quantity === 0);
    if (hasEmpty) return "critical";
    const hasLow = spaces.some((s) => s.quantity > 0 && s.quantity < 3);
    if (hasLow) return "warning";
    return "ok";
}

export function buildZoneIssues(allocations: ProductAllocation[]): ZoneIssue[] {
    const zoneMap = new Map<string, { zoneName: string; spaces: ProductAllocation[] }>();

    for (const alloc of allocations) {
        const zoneId = alloc.space.zone.id;
        if (!zoneMap.has(zoneId)) {
            zoneMap.set(zoneId, { zoneName: alloc.space.zone.name, spaces: [] });
        }
        zoneMap.get(zoneId)!.spaces.push(alloc);
    }

    const issues: ZoneIssue[] = [];
    for (const [zoneId, { zoneName, spaces }] of zoneMap) {
        const needsAttention = spaces.filter((s) => s.quantity < 3);
        if (needsAttention.length === 0) continue;

        const status = computeZoneStatus(spaces);
        issues.push({ zoneId, zoneName, status, spaces: needsAttention });
    }

    issues.sort((a, b) => {
        const order: Record<ZoneStatus, number> = { critical: 0, warning: 1, ok: 2 };
        return order[a.status] - order[b.status];
    });

    return issues;
}

export function buildZoneStatusMap(allocations: ProductAllocation[]): Map<string, ZoneStatus> {
    const statusMap = new Map<string, ZoneStatus>();
    const zoneMap = new Map<string, ProductAllocation[]>();
    for (const alloc of allocations) {
        const zoneId = alloc.space.zone.id;
        if (!zoneMap.has(zoneId)) zoneMap.set(zoneId, []);
        zoneMap.get(zoneId)!.push(alloc);
    }
    for (const [zoneId, spaces] of zoneMap) {
        statusMap.set(zoneId, computeZoneStatus(spaces));
    }
    return statusMap;
}

export function filterSpaces(spaces: ProductAllocation[], filter: FilterMode): ProductAllocation[] {
    if (filter === "empty") return spaces.filter((s) => s.quantity === 0);
    if (filter === "low") return spaces.filter((s) => s.quantity > 0 && s.quantity < 3);
    return spaces;
}

export function filterZoneIssues(zoneIssues: ZoneIssue[], filter: FilterMode): ZoneIssue[] {
    if (filter === "all") return zoneIssues;
    return zoneIssues
        .map((zone) => ({
            ...zone,
            spaces: filterSpaces(zone.spaces, filter),
        }))
        .filter((zone) => zone.spaces.length > 0);
}
