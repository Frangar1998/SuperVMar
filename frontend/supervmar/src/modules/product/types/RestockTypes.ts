import type { ProductAllocation } from "../services/AllocationService.ts";

export type ZoneStatus = "ok" | "warning" | "critical";
export type FilterMode = "all" | "empty" | "low";

export interface ZoneIssue {
    zoneId: string;
    zoneName: string;
    status: ZoneStatus;
    spaces: ProductAllocation[];
}

export const STATUS_COLORS: Record<ZoneStatus, string> = {
    ok: "#4CAF50",
    warning: "#FF9800",
    critical: "#F44336",
};
