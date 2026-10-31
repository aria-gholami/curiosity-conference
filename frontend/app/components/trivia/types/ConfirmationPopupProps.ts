export default interface ConfirmationPopupProps {
    open: boolean;
    title: string;
    message: string;
    confirmLabel: string;
    onCancel: () => void;
    onConfirm: () => void | Promise<void>;
    loading?: boolean;
}
