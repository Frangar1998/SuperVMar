import type { MouseEvent } from "react";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import TableCell from "@mui/material/TableCell";
import TableSortLabel from "@mui/material/TableSortLabel";
import type { Order } from "./OrderType.ts";

interface TableHeaderProps<T> {
    onRequestSort: (event: MouseEvent<unknown>, property: keyof T) => void;
    order: Order;
    orderBy: keyof T;
    headers: {
        id: keyof T;
        label: string;
        numeric: boolean;
    }[];
    showActionsColumn?: boolean;
}

export const TableHeaderComponent = <T, >(props: TableHeaderProps<T>) => {
    const {order, orderBy, onRequestSort, headers, showActionsColumn} = props;
    const createSortHandler =
        (property: keyof T) => (event: MouseEvent<unknown>) => {
            onRequestSort(event, property);
        };

    return (
        <TableHead>
            <TableRow>
                {headers.map((header) => (
                    <TableCell
                        key={String(header.id)}
                        align={'left'}
                        padding={'normal'}
                        sortDirection={orderBy === header.id ? order : false}
                    >
                        {String(header.id) == "image" ? (
                            header.label
                        ) : (
                            <TableSortLabel
                                active={orderBy === header.id}
                                direction={orderBy === header.id ? order : 'asc'}
                                onClick={createSortHandler(header.id)}
                            >
                                {header.label}
                            </TableSortLabel>
                        )}

                    </TableCell>
                ))}
                {showActionsColumn && (
                    <TableCell align="center" padding="normal">
                        Acciones
                    </TableCell>
                )}
            </TableRow>
        </TableHead>
    );
}