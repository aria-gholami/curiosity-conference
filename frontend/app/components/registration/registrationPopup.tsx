import { ArrowRightIcon } from '@heroicons/react/24/solid';
import { type SubmitEvent, useState } from 'react';
import type { RegistrationRequestBody } from '~/features/user/state/RegistrationRequestBody';
import { registerUser } from '~/features/user/reducers/registerUser';
import { useAppDispatch } from '~/redux/hooks';
import { useNavigate } from 'react-router';
import { useBodyScrollLock } from '~/hooks/useBodyScrollLock';
import type RegistrationPopupProps from '~/components/registration/types/RegistrationPopupProps';

export default function RegistrationPopup({ open }: RegistrationPopupProps) {
    const navigate = useNavigate();
    const dispatch = useAppDispatch();
    const [userName, setUserName] = useState('');
    const [errorMessage, setErrorMessage] = useState('');
    const MAX_USERNAME_LENGTH = 15;

    useBodyScrollLock(open);
    if (!open) {
        return null;
    }

    const handleSubmit = async (event: SubmitEvent) => {
        event.preventDefault();

        if (userName.length === 0) {
            setErrorMessage("Your username can't be empty.");
            return;
        }
        if (userName.length > MAX_USERNAME_LENGTH) {
            setErrorMessage('Your username must not exceed 15 characters.');
            return;
        }

        const registrationRequestBody: RegistrationRequestBody = {
            userName,
        };

        await dispatch(registerUser(registrationRequestBody) as any);
        navigate('/');
    };

    return (
        <div className="popup">
            <div className="popup-overlay" />
            <div className="glass popup-container">
                <div className="popup-glow-turquoise" />
                <div className="popup-glow-dark-blue" />
                <div className="popup-gradient" />
                <form onSubmit={handleSubmit} className="popup-content">
                    <div className="registration-popup-header">
                        <div className="popup-badge">Registration</div>
                        <h2 className="popup-title">Sign up now</h2>
                        <p className="popup-description">
                            Register to join interactive activities, meet other participants, and
                            explore ideas together.
                        </p>
                    </div>

                    <div className="mt-3">
                        <label htmlFor="registration-username" className="input-label">
                            Username
                        </label>

                        <input
                            id="registration-username"
                            type="text"
                            placeholder="Your username"
                            className="input"
                            maxLength={MAX_USERNAME_LENGTH}
                            value={userName}
                            required
                            onChange={(event) => setUserName(event.target.value)}
                        />

                        <div className="registration-popup-input-footer">
                            <div className="registration-popup-error-container">
                                {errorMessage && (
                                    <p className="registration-popup-error">{errorMessage}</p>
                                )}
                            </div>

                            <span
                                className={`registration-popup-character-count ${
                                    userName.length > MAX_USERNAME_LENGTH
                                        ? 'registration-popup-character-count-error'
                                        : ''
                                }`}
                            >
                                {userName.length}/{MAX_USERNAME_LENGTH}
                            </span>
                        </div>
                    </div>

                    <div className="registration-popup-actions">
                        <button type="submit" className="btn-primary registration-popup-submit">
                            <span>Register</span>
                            <ArrowRightIcon className="registration-popup-submit-icon" />
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
