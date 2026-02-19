import type { TableData } from "../../../commons/components/Table/TableData.ts";
import type { TaxTable } from "../../types/ProductTypes.ts";
import TableCell from "@mui/material/TableCell";

export const TaxesTableRow = (row: TableData<TaxTable>) => (
    <>
        <TableCell align="left">{row.data.name}</TableCell>
        <TableCell align="left">{row.data.percent}%</TableCell>
    </>
);

