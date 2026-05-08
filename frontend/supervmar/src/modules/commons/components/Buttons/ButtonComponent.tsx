import { Button } from "@mui/material";
import type { ButtonColor, ButtonSize, ButtonType, ButtonVariant } from "../../types/ButtonTypes.ts";


interface ButtonProps {
    text: string | undefined;
    type: ButtonType;
    variant: ButtonVariant;
    color: ButtonColor;
    size: ButtonSize;
    disableElevation?: boolean | undefined;
    fullWidth?: boolean | undefined;
    sx?: any;
    className?: string;
    onClick?: () => void;
    startIcon?: any;
    disabled?: boolean;
}

export const ButtonComponent = ({disabled = false, ...buttonProps}: ButtonProps
) => {
    return (
        <Button
            type={buttonProps.type}
            variant={buttonProps.variant}
            color={buttonProps.color}
            size={buttonProps.size}
            disableElevation={buttonProps.disableElevation}
            fullWidth={buttonProps.fullWidth}
            sx={buttonProps.sx}
            className={buttonProps.className}
            onClick={buttonProps.onClick}
            startIcon={buttonProps.startIcon}
            disabled={disabled}
        >
            {buttonProps.text}
        </Button>
    );
};