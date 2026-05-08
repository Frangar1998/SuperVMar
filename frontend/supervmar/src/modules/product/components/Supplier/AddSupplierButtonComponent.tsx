import { useNavigate } from "react-router";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { Add } from '@mui/icons-material';

export const AddSupplierButtonComponent = () => {
    const navigate = useNavigate();

    const handleAddSupplier = () => {
        navigate('/productos/proveedores/nuevo');
    };

    return (
        <ButtonComponent
            text="Añadir proveedor"
            type="button"
            variant="contained"
            color="primary"
            size="small"
            disableElevation
            startIcon={<Add />}
            onClick={handleAddSupplier}
        />
    );
};

