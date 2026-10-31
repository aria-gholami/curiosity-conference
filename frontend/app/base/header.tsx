import { Bars3Icon, XMarkIcon } from '@heroicons/react/24/solid';
import { useState } from 'react';
import type { UserState } from '~/features/user/state/Initial';
import type { UserDeleteRequestBody } from '~/features/user/state/UserDeleteRequestBody';
import { deleteUser } from '~/features/user/reducers/deleteUser';
import { resetCurrentRoom, resetRooms } from '~/features/quizRoom/quizRoomSlice';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import LogoutPopup from '~/components/registration/logoutPopup';
import { useMenuScrollLock } from '~/base/hooks/useMenuScrollLock';
import MobileBurgerMenu from '~/components/header/MobileBurgerMenu';

export default function Header() {
    const dispatch = useAppDispatch();
    const userState: UserState = useAppSelector((state) => state.user);
    const [logoutOpen, setLogoutOpen] = useState(false);
    const isLoggedIn = userState.userId !== '';
    const [mobileOpen, setMobileOpen] = useState(false);

    const handleLogoutClick = () => {
        setMobileOpen(false);
        if (!isLoggedIn) {
            return;
        }

        setLogoutOpen(true);
    };
    const handleLogoutConfirm = () => {
        const userDeleteRequestBody: UserDeleteRequestBody = {
            userId: userState.userId,
        };

        dispatch(deleteUser(userDeleteRequestBody) as any);
        dispatch(resetCurrentRoom() as any);
        dispatch(resetRooms() as any);
        setLogoutOpen(false);
        setMobileOpen(false);
    };

    useMenuScrollLock(mobileOpen);

    return (
        <>
            <header className="sticky-header">
                <div className="sticky-header-container">
                    <a href="/" className="sticky-header-logo-link group">
                        <img
                            src="/logo.svg"
                            alt="Curious Activities Logo"
                            className="h-12 w-auto max-w-40 object-contain transition-transform duration-300 group-hover:scale-105"
                        />
                    </a>

                    <div className="sticky-header-login-container">
                        <button
                            type="button"
                            className="btn-primary btn-sm select-none"
                            onClick={handleLogoutClick}
                        >
                            {isLoggedIn ? 'Logout' : 'Login'}
                        </button>
                    </div>

                    <button
                        type="button"
                        className="sticky-header-burger-menu-button"
                        onClick={() => setMobileOpen((open) => !open)}
                        aria-label="Toggle menu"
                        aria-expanded={mobileOpen}
                        aria-controls="mobile-menu"
                    >
                        {mobileOpen ? (
                            <XMarkIcon className="h-5 w-5" />
                        ) : (
                            <Bars3Icon className="h-5 w-5" />
                        )}
                    </button>
                </div>
            </header>

            <MobileBurgerMenu
                setMobileOpen={setMobileOpen}
                mobileOpen={mobileOpen}
                isLoggedIn={isLoggedIn}
                handleLogoutClick={handleLogoutClick}
            />

            <LogoutPopup
                open={logoutOpen}
                onCancel={() => setLogoutOpen(false)}
                onConfirm={handleLogoutConfirm}
            />
        </>
    );
}
