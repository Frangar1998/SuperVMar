import { useNavigate } from "react-router";
import { ButtonComponent } from "../../../commons/components/ButtonComponent.tsx";
import { Add } from '@mui/icons-material';


export const AddProductButtonComponent = () => {
    const navigate = useNavigate();

    const handleAddProduct = () => {
        navigate(`/productos/nuevo`);
    };

    return (
        <ButtonComponent
            text="Añadir producto"
            type="button"
            variant="contained"
            color="primary"
            size="small"
            disableElevation
            startIcon={<Add />}
            onClick={handleAddProduct}
        />
    );


};