import { ArrowRightIcon } from '@heroicons/react/24/solid';
import { useState } from 'react';
import type { RegistrationRequestBody } from '~/features/user/state/RegistrationRequestBody';
import { registerUser } from '~/features/user/reducers/registerUser';
import { useAppDispatch } from '~/redux/hooks';
import { useNavigate } from 'react-router';

type RegistrationPopupProps = {
    open: boolean;
};

export default function RegistrationPopup({ open }: RegistrationPopupProps) {
    const navigate = useNavigate();
    const dispatch = useAppDispatch();
    const [userName, setUserName] = useState('');
    const [errorMessage, setErrorMessage] = useState('');
    const MAX_USERNAME_LENGTH = 15;

    if (!open) return null;

    const handleSubmit = async (event: React.FormEvent) => {
        event.preventDefault();

        if (userName.length === 0) {
            setErrorMessage('Your username can\'t be empty.');
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
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div className="absolute inset-0 bg-dark-blue/55 backdrop-blur-md" />

            <div className="glass relative w-full max-w-lg overflow-hidden rounded-[2rem] border border-white/20 bg-white/90">
                <div className="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-turquoise/15 blur-3xl" />
                <div className="pointer-events-none absolute -left-12 bottom-0 h-32 w-32 rounded-full bg-dark-blue/20 blur-3xl" />
                <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,0.6)_0%,transparent_55%)]" />

                <form onSubmit={handleSubmit} className="relative p-6 sm:p-8">
                    <div className="flex items-start justify-between gap-4">
                        <div>
                            <div className="inline-flex items-center gap-2 rounded-full border border-turquoise/20 bg-turquoise/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-dark-blue">
                                Registration
                            </div>

                            <h2 className="mt-4 text-2xl font-semibold tracking-[-0.02em] text-dark-blue sm:text-3xl">
                                Sign up now
                            </h2>

                            <p className="mt-3 text-sm leading-6 text-card-text sm:text-base">
                                Register to join interactive activities, meet other participants,
                                and explore ideas together.
                            </p>
                        </div>
                    </div>

                    <div className="mt-8 space-y-4">
                        <div>
                            <label className="input-label">Username</label>

                            <input
                                type="text"
                                placeholder="Your username"
                                className="input"
                                maxLength={MAX_USERNAME_LENGTH}
                                value={userName}
                                required
                                onChange={(event) => setUserName(event.target.value)}
                            />
                            <div className="mt-2 flex items-start justify-between gap-3">
                                <div className="min-w-0 flex-1">
                                    {errorMessage && (
                                        <p className="text-xs leading-5 text-red-600">
                                            {errorMessage}
                                        </p>
                                    )}
                                </div>

                                <span
                                    className={`shrink-0 text-xs leading-5 ${
                                        userName.length > MAX_USERNAME_LENGTH
                                            ? 'font-semibold text-red-600'
                                            : 'text-card-text'
                                    }`}
                                >
                                    {userName.length}/{MAX_USERNAME_LENGTH}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                        <button
                            type="submit"
                            className="btn-primary group gap-2.5 px-6 py-3 shadow-[0_8px_28px_rgba(230,103,39,0.3)] hover:shadow-[0_12px_36px_rgba(230,103,39,0.4)]"
                        >
                            <span>Register</span>
                            <ArrowRightIcon className="h-4 w-4" />
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
