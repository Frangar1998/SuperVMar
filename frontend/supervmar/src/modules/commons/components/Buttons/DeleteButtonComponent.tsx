import { Delete } from '@mui/icons-material';
import { ButtonComponent } from "./ButtonComponent.tsx";

interface DeleteButtonProps {
    text?: string;
    onClick: () => void;
    disabled?: boolean;
    size?: "small" | "medium" | "large";
    variant?: "contained" | "outlined" | "text";
}

export const DeleteButtonComponent = ({
      text = "Eliminar",
      onClick,
      disabled = false,
      size = "medium",
      variant = "outlined"
  }: DeleteButtonProps) => {
    return (
        <ButtonComponent
            text={text}
            type="button"
            variant={variant}
            color="error"
            size={size}
            startIcon={<Delete />}
            onClick={onClick}
            disabled={disabled}
        />
    );
};