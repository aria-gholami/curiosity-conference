import { useBodyScrollLock } from '~/hooks/useBodyScrollLock';
import type LogoutPopupProps from '~/components/registration/types/LogoutPopupProps';

export default function LogoutPopup({ open, onCancel, onConfirm }: LogoutPopupProps) {
    useBodyScrollLock(open);
    if (!open) {
        return null;
    }

    return (
        <div className="logout-popup">
            <div className="logout-popup-overlay" onClick={onCancel} />

            <div className="glass logout-popup-container">
                <div className="logout-popup-glow-turquoise" />
                <div className="logout-popup-glow-dark-blue" />
                <div className="logout-popup-gradient" />

                <div className="logout-popup-content">
                    <div className="logout-popup-header">
                        <div className="logout-popup-badge">Logout</div>

                        <h2 className="logout-popup-title">Leave session?</h2>

                        <p className="logout-popup-description">
                            Are you sure you want to log out?
                        </p>
                    </div>

                    <div className="logout-popup-actions">
                        <button
                            type="button"
                            onClick={onConfirm}
                            className="btn-primary logout-popup-confirm"
                        >
                            Logout
                        </button>

                        <button
                            type="button"
                            onClick={onCancel}
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
