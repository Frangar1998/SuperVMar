import type { TableData } from "../../../commons/components/Table/TableData.ts";
import type { JobTable } from "../../types/UserTypes.ts";
import TableCell from "@mui/material/TableCell";

export const JobsTableRow = (row: TableData<JobTable>) => (
    <>
        <TableCell align="left">{row.data.name}</TableCell>
    </>
);
