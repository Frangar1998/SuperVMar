import Box from "@mui/material/Box";
import ToggleButton from "@mui/material/ToggleButton";
import ToggleButtonGroup from "@mui/material/ToggleButtonGroup";
import TextField from "@mui/material/TextField";
import Typography from "@mui/material/Typography";
import type { DateFilter } from "../types/DashboardTypes.ts";

interface DateFilterProps {
    value: DateFilter;
    customDateFrom: string;
    customDateTo: string;
    onChange: (value: DateFilter) => void;
    onCustomDateFromChange: (date: string) => void;
    onCustomDateToChange: (date: string) => void;
}

export const DateFilterComponent = ({ value, customDateFrom, customDateTo, onChange, onCustomDateFromChange, onCustomDateToChange }: DateFilterProps) => {
    const handleChange = (_: React.MouseEvent<HTMLElement>, newValue: DateFilter | null) => {
        if (newValue) onChange(newValue);
    };

    return (
        <Box sx={{ display: "flex", alignItems: "center", gap: 2, mb: 3, flexWrap: "wrap" }}>
            <ToggleButtonGroup value={value} exclusive onChange={handleChange} size="small">
                <ToggleButton value="today">Hoy</ToggleButton>
                <ToggleButton value="week">7 días</ToggleButton>
                <ToggleButton value="month">30 días</ToggleButton>
                <ToggleButton value="year">1 Año</ToggleButton>
                <ToggleButton value="custom">Personalizado</ToggleButton>
            </ToggleButtonGroup>
            {value === "custom" && (
                <Box sx={{ display: "flex", alignItems: "center", gap: 1 }}>
                    <TextField
                        type="date"
                        size="small"
                        label="Desde"
                        value={customDateFrom}
                        onChange={(e) => onCustomDateFromChange(e.target.value)}
                        slotProps={{ inputLabel: { shrink: true } }}
                    />
                    <Typography variant="body2" color="text.secondary">—</Typography>
                    <TextField
                        type="date"
                        size="small"
                        label="Hasta"
                        value={customDateTo}
                        onChange={(e) => onCustomDateToChange(e.target.value)}
                        slotProps={{ inputLabel: { shrink: true } }}
                    />
                </Box>
            )}
        </Box>
    );
};
