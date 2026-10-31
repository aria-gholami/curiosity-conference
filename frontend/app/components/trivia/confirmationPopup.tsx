import { useBodyScrollLock } from '~/hooks/useBodyScrollLock';
import type ConfirmationPopupProps from '~/components/trivia/types/ConfirmationPopupProps';

export default function ConfirmationPopup({
    open,
    title,
    message,
    confirmLabel,
    onCancel,
    onConfirm,
    loading = false,
}: ConfirmationPopupProps) {
    useBodyScrollLock(open);

    if (!open) {
        return null;
    }

    return (
        <div className="popup" role="dialog" aria-modal="true" aria-labelledby="confirmation-title">
            <div className="popup-overlay" onClick={loading ? undefined : onCancel} />
            <div className="glass popup-container">
                <div className="popup-glow-turquoise" />
                <div className="popup-glow-dark-blue" />
                <div className="popup-gradient" />
                <div className="popup-content">
                    <div className="popup-badge">Confirmation</div>
                    <h2 id="confirmation-title" className="popup-title">
                        {title}
                    </h2>
                    <p className="popup-description">{message}</p>

                    <div className="popup-actions">
                        <button
                            type="button"
                            onClick={onConfirm}
                            disabled={loading}
                            className="btn-primary px-6 py-3"
                        >
                            {loading ? 'Processing...' : confirmLabel}
                        </button>

                        <button
                            type="button"
                            onClick={onCancel}
                            disabled={loading}
                            className="btn-secondary-light btn-lg"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
