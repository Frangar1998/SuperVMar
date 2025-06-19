import Toolbar from "@mui/material/Toolbar";
import Box from "@mui/material/Box";
import { FormControl, InputLabel, MenuItem, Select, TextField } from "@mui/material";

interface TableToolbarProps<T> {
    headers: {
        id: keyof T;
        label: string;
        numeric: boolean;
    }[];
    search: string;
    searchField: keyof T;
    onSearchChange: (searchString: string) => void;
    onSearchFieldChange: (field: keyof T) => void;
}

export const TableToolbarComponent = <T, >(props: TableToolbarProps<T>) => {
    const {headers, search, searchField, onSearchChange, onSearchFieldChange} = props;
    return (
        <Toolbar
            sx={[
                {
                    pl: {sm: 2},
                    pr: {xs: 1, sm: 1},
                    gap: 2
                }
            ]}
        >
            <Box sx={{display: 'flex', gap: 2, alignItems: 'center'}}>
                <FormControl sx={{minWidth: 120}}>
                    <InputLabel id="search-field-label">Buscar por</InputLabel>
                    <Select
                        labelId="search-field-label"
                        value={searchField}
                        label="Buscar por"
                        onChange={(event) => onSearchFieldChange(event.target.value as keyof T)}
                        size={"small"}
                    >
                        {headers.map((header) => (
                            <MenuItem key={String(header.id)} value={String(header.id)}>
                                {header.label}
                            </MenuItem>
                        ))}
                    </Select>
                </FormControl>
                <TextField
                    size={"small"}
                    variant={"outlined"}
                    placeholder={"Buscar..."}
                    value={search}
                    onChange={(event) => onSearchChange(event.target.value)}
                />
            </Box>
        </Toolbar>
    );
};