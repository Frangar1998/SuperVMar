import {
    Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle
} from "@mui/material";
import { ButtonComponent } from "./Buttons/ButtonComponent.tsx";

interface ConfirmDialogProps {
    open: boolean;
    title: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    confirmColor?: "primary" | "error" | "warning" | "success";
    onConfirm: () => void;
    onCancel: () => void;
    isLoading?: boolean;
}

export const ConfirmDialogComponent = (confirmDialogProps: ConfirmDialogProps) => {
    return (
        <Dialog
            open={confirmDialogProps.open}
            onClose={confirmDialogProps.onCancel}
            aria-labelledby="confirm-dialog-title"
            aria-describedby="confirm-dialog-description"
        >
            <DialogTitle id="confirm-dialog-title">
                {confirmDialogProps.title}
            </DialogTitle>
            <DialogContent>
                <DialogContentText id="confirm-dialog-description">
                    {confirmDialogProps.message}
                </DialogContentText>
            </DialogContent>
            <DialogActions>
                <ButtonComponent
                    text={confirmDialogProps.cancelText}
                    type="button"
                    variant="text"
                    color="primary"
                    size="medium"
                    onClick={confirmDialogProps.onCancel}
                    disabled={confirmDialogProps.isLoading}
                />
                <ButtonComponent
                    text={confirmDialogProps.confirmText}
                    type="button"
                    variant="contained"
                    color={confirmDialogProps.confirmColor}
                    size="medium"
                    onClick={confirmDialogProps.onConfirm}
                    disabled={confirmDialogProps.isLoading}
                />
            </DialogActions>
        </Dialog>
    );
};