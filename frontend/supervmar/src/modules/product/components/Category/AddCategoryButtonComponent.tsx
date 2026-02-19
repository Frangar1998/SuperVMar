import { useNavigate } from "react-router";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { Add } from '@mui/icons-material';

export const AddCategoryButtonComponent = () => {
    const navigate = useNavigate();

    const handleAddCategory = () => {
        navigate('/productos/categorias/nueva');
    };

    return (
        <ButtonComponent
            text="Añadir categoría"
            type="button"
            variant="contained"
            color="primary"
            size="small"
            disableElevation
            startIcon={<Add />}
            onClick={handleAddCategory}
        />
    );
};

