import { useState } from "react";
import { ApiError } from "../services/HttpService.ts";

interface UseDeleteConfirmationProps {
    onDelete: () => Promise<void>;
    itemName?: string;
}

export const useDeleteConfirmation = ({ onDelete, itemName }: UseDeleteConfirmationProps) => {
    const [isOpen, setIsOpen] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const openDialog = () => setIsOpen(true);
    const closeDialog = () => setIsOpen(false);
    const clearError = () => setError(null);

    const handleConfirm = async () => {
        try {
            setIsDeleting(true);
            await onDelete();
            closeDialog();
        } catch (err) {
            console.error('Error deleting item:', err);
            closeDialog();
            setIsDeleting(false);
            if (err instanceof ApiError) {
                setError(err.message);
            } else {
                setError('Ha ocurrido un error inesperado al eliminar.');
            }
        }
    };

    const getDialogTitle = () => `Eliminar ${itemName || 'elemento'}`;

    const getDialogMessage = (name?: string) =>
        name
            ? `¿Estás seguro de que deseas eliminar "${name}"? Esta acción no se puede deshacer.`
            : `¿Estás seguro de que deseas eliminar este ${itemName || 'elemento'}? Esta acción no se puede deshacer.`;

    return {
        isOpen,
        isDeleting,
        error,
        openDialog,
        closeDialog,
        clearError,
        handleConfirm,
        getDialogTitle,
        getDialogMessage
    };
};