import { type ChangeEvent, useState, useEffect } from "react";
import {
    Dialog, DialogActions, DialogContent, DialogTitle, TextField
} from "@mui/material";
import { ButtonComponent } from "../../commons/components/Buttons/ButtonComponent.tsx";

interface ZoneDialogProps {
    open: boolean;
    title: string;
    initialName?: string;
    onConfirm: (name: string) => void;
    onCancel: () => void;
}

export const ZoneDialogComponent = ({ open, title, initialName = '', onConfirm, onCancel }: ZoneDialogProps) => {
    const [name, setName] = useState(initialName);
    const [error, setError] = useState('');

    useEffect(() => {
        if (open) {
            setName(initialName);
            setError('');
        }
    }, [open, initialName]);

    const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
        setName(e.target.value);
        if (error) setError('');
    };

    const handleConfirm = () => {
        if (!name.trim()) {
            setError('El nombre de la zona es obligatorio');
            return;
        }
        onConfirm(name.trim());
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleConfirm();
        }
    };

    return (
        <Dialog open={open} onClose={onCancel} maxWidth="xs" fullWidth>
            <DialogTitle>{title}</DialogTitle>
            <DialogContent>
                <TextField
                    autoFocus
                    fullWidth
                    label="Nombre de la zona"
                    value={name}
                    onChange={handleChange}
                    onKeyDown={handleKeyDown}
                    error={!!error}
                    helperText={error}
                    sx={{ mt: 1 }}
                />
            </DialogContent>
            <DialogActions>
                <ButtonComponent
                    text="Cancelar"
                    type="button"
                    variant="text"
                    color="primary"
                    size="medium"
                    onClick={onCancel}
                />
                <ButtonComponent
                    text="Confirmar"
                    type="button"
                    variant="contained"
                    color="primary"
                    size="medium"
                    onClick={handleConfirm}
                />
            </DialogActions>
        </Dialog>
    );
};


