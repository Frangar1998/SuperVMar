import type { TableData } from "../../../commons/components/Table/TableData.ts";
import type { SupplierTable } from "../../types/ProductTypes.ts";
import TableCell from "@mui/material/TableCell";

export const SuppliersTableRow = (row: TableData<SupplierTable>) => (
    <>
        <TableCell align="left">{row.data.name}</TableCell>
        <TableCell align="left">{row.data.phone}</TableCell>
        <TableCell align="left">{row.data.email}</TableCell>
        <TableCell align="left">{row.data.contact}</TableCell>
    </>
);

