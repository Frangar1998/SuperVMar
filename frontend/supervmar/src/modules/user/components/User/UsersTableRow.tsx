import type { TableData } from "../../../commons/components/Table/TableData.ts";
import type { UserTable } from "../../types/UserTypes.ts";
import TableCell from "@mui/material/TableCell";
import { Chip } from "@mui/material";

export const UsersTableRow = (row: TableData<UserTable>) => (
    <>
        <TableCell align="left">
            {row.data.username}
            {row.data.isAdmin === 1 && (
                <Chip label="Admin" size="small" color="primary" sx={{ ml: 1 }} />
            )}
        </TableCell>
        <TableCell align="left">{row.data.name}</TableCell>
        <TableCell align="left">{row.data.surname}</TableCell>
        <TableCell align="left">{row.data.email}</TableCell>
        <TableCell align="left">{row.data.job}</TableCell>
    </>
);
