import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { DeleteButtonComponent } from "../../../commons/components/Buttons/DeleteButtonComponent.tsx";
import { Add, HighlightAlt, Edit, OpenWith, ViewModule } from '@mui/icons-material';
import { Chip } from "@mui/material";
import Box from "@mui/material/Box";
import type { ZoneFormData } from "../../types/SupermarketTypes.ts";

interface MapToolbarProps {
    isCreating: boolean;
    isRepositioning: boolean;
    selectedZone: ZoneFormData | null;
    onAddZone: () => void;
    onEditZone: () => void;
    onRepositionZone: () => void;
    onManageSpaces: () => void;
    onDeleteZone: () => void;
    onCancelCreating: () => void;
}

export const MapToolbar = ({
    isCreating,
    isRepositioning,
    selectedZone,
    onAddZone,
    onEditZone,
    onRepositionZone,
    onManageSpaces,
    onDeleteZone,
    onCancelCreating,
}: MapToolbarProps) => {
    return (
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, mb: 1, flexWrap: 'wrap' }}>
            {!isCreating && !isRepositioning && !selectedZone && (
                <ButtonComponent
                    text="Añadir zona"
                    type="button"
                    variant="contained"
                    color="primary"
                    size="small"
                    disableElevation
                    startIcon={<Add />}
                    onClick={onAddZone}
                />
            )}

            {isCreating && !isRepositioning && (
                <>
                    <Chip
                        icon={<HighlightAlt />}
                        label="Arrastra sobre el mapa para crear una zona"
                        color="primary"
                        variant="outlined"
                    />
                    <ButtonComponent
                        text="Cancelar"
                        type="button"
                        variant="text"
                        color="primary"
                        size="small"
                        onClick={onCancelCreating}
                    />
                </>
            )}

            {isRepositioning && selectedZone && (
                <>
                    <Chip
                        icon={<HighlightAlt />}
                        label={`Arrastra sobre el mapa para reubicar "${selectedZone.name}"`}
                        color="warning"
                        variant="outlined"
                    />
                    <ButtonComponent
                        text="Cancelar"
                        type="button"
                        variant="text"
                        color="primary"
                        size="small"
                        onClick={onCancelCreating}
                    />
                </>
            )}

            {selectedZone && !isCreating && !isRepositioning && (
                <>
                    <Chip
                        label={`Zona seleccionada: ${selectedZone.name}`}
                        color="primary"
                        variant="filled"
                        sx={{ fontWeight: 'bold' }}
                    />
                    <ButtonComponent
                        text="Editar nombre"
                        type="button"
                        variant="outlined"
                        color="primary"
                        size="small"
                        startIcon={<Edit />}
                        onClick={onEditZone}
                    />
                    <ButtonComponent
                        text="Editar posición"
                        type="button"
                        variant="outlined"
                        color="warning"
                        size="small"
                        startIcon={<OpenWith />}
                        onClick={onRepositionZone}
                    />
                    <ButtonComponent
                        text="Gestionar espacios"
                        type="button"
                        variant="outlined"
                        color="success"
                        size="small"
                        startIcon={<ViewModule />}
                        onClick={onManageSpaces}
                    />
                    <DeleteButtonComponent
                        text="Eliminar zona"
                        onClick={onDeleteZone}
                    />
                </>
            )}
        </Box>
    );
};

