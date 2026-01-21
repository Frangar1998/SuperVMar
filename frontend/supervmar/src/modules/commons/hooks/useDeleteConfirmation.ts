import { useState } from "react";

interface UseDeleteConfirmationProps {
    onDelete: () => Promise<void>;
    itemName?: string;
}

export const useDeleteConfirmation = ({ onDelete, itemName }: UseDeleteConfirmationProps) => {
    const [isOpen, setIsOpen] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);

    const openDialog = () => setIsOpen(true);
    const closeDialog = () => setIsOpen(false);

    const handleConfirm = async () => {
        try {
            setIsDeleting(true);
            await onDelete();
            closeDialog();
        } catch (error) {
            console.error('Error deleting item:', error);
            setIsDeleting(false);
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
        openDialog,
        closeDialog,
        handleConfirm,
        getDialogTitle,
        getDialogMessage
    };
};