import { useNavigate } from "react-router";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { Add } from '@mui/icons-material';

export const AddJobButtonComponent = () => {
    const navigate = useNavigate();

    const handleAddJob = () => {
        navigate('/usuarios/trabajos/nuevo');
    };

    return (
        <ButtonComponent
            text="Añadir trabajo"
            type="button"
            variant="contained"
            color="primary"
            size="small"
            disableElevation
            startIcon={<Add />}
            onClick={handleAddJob}
        />
    );
};
