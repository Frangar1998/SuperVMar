import type { TableData } from "../../../commons/components/Table/TableData.ts";
import type { CategoryTable } from "../../types/ProductTypes.ts";
import TableCell from "@mui/material/TableCell";

export const CategoriesTableRow = (row: TableData<CategoryTable>) => (
    <>
        <TableCell align="left">{row.data.name}</TableCell>
    </>
);
