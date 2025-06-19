import { type ChangeEvent, type MouseEvent, type ReactNode, useEffect, useMemo, useState } from "react";
import Box from '@mui/material/Box';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TablePagination from '@mui/material/TablePagination';
import TableRow from '@mui/material/TableRow';
import Paper from '@mui/material/Paper';
import type { Order } from "./OrderType.ts";
import { TableHeaderComponent } from "./TableHeaderComponent.tsx";
import type { TableData } from "./TableData.ts";
import { TableToolbarComponent } from "./TableToolbarComponent.tsx";

const descendingComparator = <T, >(a: TableData<T>, b: TableData<T>, orderBy: keyof T) => {
    if (b.data[orderBy] < a.data[orderBy]) {
        return -1;
    }
    if (b.data[orderBy] > a.data[orderBy]) {
        return 1;
    }
    return 0;
}

const getComparator = <T,>(
    order: Order,
    orderBy: keyof T,
): (
    a: TableData<T>,
    b: TableData<T>,
) => number => {
    return order === 'desc'
        ? (a, b) => descendingComparator(a, b, orderBy)
        : (a, b) => -descendingComparator(a, b, orderBy);
}

interface TableComponentProps<T> {
    tableData: TableData<T>[];
    initialSortKey: keyof T;
    headers: {
        id: keyof T;
        label: string;
        numeric: boolean;
    }[];
    renderRow: (row: TableData<T>) => ReactNode;
    getRowId: (row: TableData<T>) => string;
    onRowClick?: (row: TableData<T>) => void;
}

export const TableComponent = <T,>(props: TableComponentProps<T>) => {
    const {tableData, initialSortKey, headers, renderRow, getRowId, onRowClick} = props;

    const [order, setOrder] = useState<Order>('asc');
    const [orderBy, setOrderBy] = useState<keyof T>(initialSortKey);
    const [page, setPage] = useState(0);
    const [rowsPerPage, setRowsPerPage] = useState(5);
    const [search, setSearch] = useState('');
    const [searchField, setSearchField] = useState<keyof T>(initialSortKey);

    const handleRequestSort = (
        _event: MouseEvent<unknown>,
        property: keyof T,
    ) => {
        const isAsc = orderBy === property && order === 'asc';
        setOrder(isAsc ? 'desc' : 'asc');
        setOrderBy(property);
    };

    const handleClick = (_event: MouseEvent<unknown>, row: TableData<T>) => {
        onRowClick?.(row);
    };

    const handleChangePage = (_event: unknown, newPage: number) => {
        setPage(newPage);
    };

    const handleChangeRowsPerPage = (event: ChangeEvent<HTMLInputElement>) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(0);
    };

    // Avoid a layout jump when reaching the last page with empty rows.
    const emptyRows = page > 0 ? Math.max(0, (1 + page) * rowsPerPage - tableData.length) : 0;

    const filteredData = useMemo(() => {
        return tableData.filter((row) => {
            const value = row.data[searchField];
            if (value == null) {
                return false;
            }
            return String(value).toLowerCase().includes(search.toLowerCase());
        })
    }, [tableData, search, searchField]);

    const visibleRows = useMemo(
        () =>
            [...filteredData]
                .sort(getComparator(order, orderBy))
                .slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage),
        [filteredData, order, orderBy, page, rowsPerPage],
    );

    useEffect(() => {
        setPage(0);
    }, [search]);

    return (
        <Box sx={{width: '100%'}}>
            <Paper sx={{width: '100%', mb: 2}}>
                <TableToolbarComponent
                    headers={headers}
                    search={search}
                    searchField={searchField}
                    onSearchChange={setSearch}
                    onSearchFieldChange={setSearchField}
                />
                <TableContainer>
                    <Table
                        sx={{minWidth: 750}}
                        aria-labelledby="products"
                        size={'medium'}
                    >
                        <TableHeaderComponent
                            order={order}
                            orderBy={orderBy}
                            onRequestSort={handleRequestSort}
                            headers={headers}
                        />
                        <TableBody>
                            {visibleRows.map((row) => {
                                return (
                                    <TableRow
                                        hover
                                        onClick={(event) => handleClick(event, row)}
                                        tabIndex={-1}
                                        key={getRowId(row)}
                                        sx={{cursor: 'pointer'}}
                                    >
                                        {renderRow(row)}
                                    </TableRow>
                                );
                            })}
                            {emptyRows > 0 && (
                                <TableRow
                                    style={{
                                        height: 53 * emptyRows,
                                    }}
                                >
                                    <TableCell colSpan={headers.length}/>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </TableContainer>
                <TablePagination
                    rowsPerPageOptions={[5, 10, 25, 100]}
                    component="div"
                    count={filteredData.length}
                    rowsPerPage={rowsPerPage}
                    page={page}
                    onPageChange={handleChangePage}
                    onRowsPerPageChange={handleChangeRowsPerPage}
                />
            </Paper>
        </Box>
    );
}