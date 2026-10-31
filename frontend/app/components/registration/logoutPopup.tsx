import { useBodyScrollLock } from '~/hooks/useBodyScrollLock';
import type LogoutPopupProps from '~/components/registration/types/LogoutPopupProps';

export default function LogoutPopup({ open, onCancel, onConfirm }: LogoutPopupProps) {
    useBodyScrollLock(open);
    if (!open) {
        return null;
    }

    return (
        <div className="popup" role="dialog" aria-modal="true" aria-labelledby="logout-title">
            <div className="popup-overlay" onClick={onCancel} />
            <div className="glass popup-container">
                <div className="popup-glow-turquoise" />
                <div className="popup-glow-dark-blue" />
                <div className="popup-gradient" />
                <div className="popup-content">
                    <div className="popup-badge">Logout</div>
                    <h2 id="logout-title" className="popup-title">
                        Leave session?
                    </h2>
                    <p className="popup-description">Are you sure you want to log out?</p>

                    <div className="popup-actions">
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
