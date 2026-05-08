import { useNavigate } from "react-router";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { Add } from '@mui/icons-material';

export const AddTaxButtonComponent = () => {
    const navigate = useNavigate();

    const handleAddTax = () => {
        navigate('/productos/iva/nuevo');
    };

    return (
        <ButtonComponent
            text="Añadir IVA"
            type="button"
            variant="contained"
            color="primary"
            size="small"
            disableElevation
            startIcon={<Add />}
            onClick={handleAddTax}
        />
    );
};

