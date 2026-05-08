import Box from "@mui/material/Box";
import ToggleButtonGroup from "@mui/material/ToggleButtonGroup";
import ToggleButton from "@mui/material/ToggleButton";
import type { FilterMode } from "../../types/RestockTypes.ts";

interface RestockFilterToggleProps {
    value: FilterMode;
    onChange: (value: FilterMode) => void;
}

export const RestockFilterToggle = ({ value, onChange }: RestockFilterToggleProps) => {
    const handleChange = (_: React.MouseEvent<HTMLElement>, newValue: FilterMode | null) => {
        if (newValue !== null) onChange(newValue);
    };

    return (
        <Box sx={{ mb: 2, display: "flex", justifyContent: { xs: "center", md: "flex-start" } }}>
            <ToggleButtonGroup
                value={value}
                exclusive
                onChange={handleChange}
                size="small"
                sx={{
                    "& .MuiToggleButton-root": {
                        px: { xs: 1.5, md: 2 },
                        fontSize: { xs: "0.75rem", md: "0.875rem" },
                    },
                }}
            >
                <ToggleButton value="all">Todos</ToggleButton>
                <ToggleButton value="empty">Vacíos</ToggleButton>
                <ToggleButton value="low">Stock bajo</ToggleButton>
            </ToggleButtonGroup>
        </Box>
    );
};
