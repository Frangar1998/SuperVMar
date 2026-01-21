import { ButtonComponent } from "../../commons/components/Buttons/ButtonComponent.tsx";

export const LoginButtonComponent = () => {
    return (
        <ButtonComponent
            text="Iniciar sesión"
            type="submit"
            variant="contained"
            color="primary"
            size="small"
            disableElevation
            fullWidth
            sx={{
                my: 2,
                textTransform: 'none',
                fontSize: '1rem',
            }}
        />
    );
};