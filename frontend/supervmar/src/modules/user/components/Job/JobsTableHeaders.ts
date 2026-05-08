import type { JobTableHeader, JobTable } from "../../types/UserTypes.ts";

export const JOBS_TABLE_HEADERS: JobTableHeader[] = [
    {
        id: 'name' as keyof JobTable,
        numeric: false,
        label: 'Nombre'
    },
];
