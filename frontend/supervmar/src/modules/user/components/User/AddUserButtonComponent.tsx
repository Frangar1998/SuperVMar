import { useNavigate } from "react-router";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { Add } from '@mui/icons-material';

export const AddUserButtonComponent = () => {
    const navigate = useNavigate();

    const handleAddUser = () => {
        navigate('/usuarios/lista/nuevo');
    };

    return (
        <ButtonComponent
            text="Añadir usuario"
            type="button"
            variant="contained"
            color="primary"
            size="small"
            disableElevation
            startIcon={<Add />}
            onClick={handleAddUser}
        />
    );
};
