import {
    Bars3Icon,
    XMarkIcon,
    HomeIcon,
    TrophyIcon,
    ChevronRightIcon,
    UserCircleIcon,
} from '@heroicons/react/24/solid';
import { useState } from 'react';
import BurgerMenu from 'react-burger-menu';
import { useNavigate } from 'react-router';
import type { UserState } from '~/features/user/state/Initial';
import type { UserDeleteRequestBody } from '~/features/user/state/UserDeleteRequestBody';
import { deleteUser } from '~/features/user/reducers/deleteUser';
import { resetCurrentRoom, resetRooms } from '~/features/quizRoom/quizRoomSlice';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import LogoutPopup from '~/components/registration/logoutPopup';
import { ArrowRightStartOnRectangleIcon } from '@heroicons/react/16/solid';
import { MENU_STYLES } from '~/shared/styles/menuStyles';
import { useMenuScrollLock } from '~/base/hooks/useMenuScrollLock';
const { slide: Menu } = BurgerMenu;

export default function Header() {
    const dispatch = useAppDispatch();
    const navigate = useNavigate();
    const userState: UserState = useAppSelector((state) => state.user);
    const [mobileOpen, setMobileOpen] = useState(false);
    const [logoutOpen, setLogoutOpen] = useState(false);
    const isLoggedIn = userState.userId !== '';

    const handleNavigate = (path: string) => {
        setMobileOpen(false);
        navigate(path);
    };
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
            <header className="sticky top-0 z-50 border-b border-white/55 bg-white/80 backdrop-blur-[20px]">
                <div className="mx-auto flex max-w-6xl items-center justify-between px-6 py-3.5 sm:px-8">
                    <a href="/" className="group flex items-center gap-3">
                        <img
                            src="/logo.svg"
                            alt="Curious Activities Logo"
                            className="h-12 w-auto max-w-40 object-contain transition-transform duration-300 group-hover:scale-105"
                        />
                    </a>

                    <div className="hidden md:block">
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
                        className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/70 bg-white/60 text-slate-600 shadow-sm transition-all duration-200 hover:bg-white active:scale-95 md:hidden"
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

            <div className="md:hidden">
                <Menu
                    right
                    width="min(320px, 88vw)"
                    isOpen={mobileOpen}
                    onStateChange={(state) => setMobileOpen(state.isOpen)}
                    customBurgerIcon={false}
                    customCrossIcon={false}
                    disableAutoFocus
                    styles={MENU_STYLES}
                    id="mobile-menu"
                >
                    <div className="relative flex h-full w-full min-w-0 flex-col overflow-x-hidden overflow-y-auto bg-(image:--background-main) text-white">
                        <div className="pointer-events-none absolute -right-20 top-0 h-56 w-56 rounded-full bg-turquoise-glow blur-3xl" />
                        <div className="pointer-events-none absolute -left-24 bottom-20 h-48 w-48 rounded-full bg-dark-blue-glow blur-3xl" />
                        <div className="relative flex shrink-0 items-center justify-between gap-3 border-b border-white/10 px-5 py-6">
                            <div className="flex min-w-0 items-center gap-3">
                                <img
                                    src="/logo.svg"
                                    alt="Curious Activities Logo"
                                    className="h-12 w-12 shrink-0 object-contain transition-transform duration-300 group-hover:scale-105"
                                />

                                <div className="min-w-0">
                                    <p className="text-sm font-semibold tracking-wide text-white">
                                        Curious Activities
                                    </p>

                                    <p className="mt-0.5 text-[10px] uppercase tracking-wider text-text-muted">
                                        Conference Experience
                                    </p>
                                </div>
                            </div>
                        </div>

                        <nav className="relative flex min-h-0 min-w-0 flex-1 flex-col px-5 pt-8">
                            <div className="mb-5 flex items-center gap-2 px-3">
                                <span className="h-1.5 w-1.5 shrink-0 rounded-full bg-turquoise" />

                                <span className="text-[10px] font-semibold uppercase tracking-[0.18em] text-text-muted">
                                    Explore Activities
                                </span>
                            </div>

                            <div className="flex flex-col gap-2">
                                <button
                                    type="button"
                                    onClick={() => handleNavigate('/')}
                                    className="cursor-pointer transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md active:scale-95 active:translate-y-0 active:shadow-sm active:brightness-95 group flex w-full min-w-0 items-center gap-3 rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-left transition-all duration-200 hover:border-turquoise/30 hover:bg-white/15 select-none"
                                >
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/10">
                                        <HomeIcon className="h-5 w-5 text-turquoise" />
                                    </div>

                                    <span className="min-w-0 flex-1 text-sm font-semibold text-white">
                                        Home
                                    </span>

                                    <ChevronRightIcon className="h-4 w-4 shrink-0 text-white/40 transition-transform group-hover:translate-x-1" />
                                </button>

                                <button
                                    type="button"
                                    onClick={() => handleNavigate('/trivia/list')}
                                    className="cursor-pointer transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md active:scale-95 active:translate-y-0 active:shadow-sm active:brightness-95 group flex w-full min-w-0 items-center gap-3 rounded-2xl border border-white/10 bg-white/10 px-4 py-4 text-left transition-all duration-200 hover:border-accent-orange/30 hover:bg-white/15 select-none"
                                >
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-orange-400/20 bg-orange-400/10">
                                        <TrophyIcon className="h-5 w-5 text-accent-orange" />
                                    </div>

                                    <span className="min-w-0 flex-1 text-sm font-semibold text-white">
                                        Trivia Challenge
                                    </span>

                                    <ChevronRightIcon className="h-4 w-4 shrink-0 text-white/40 transition-transform group-hover:translate-x-1" />
                                </button>
                            </div>

                            <div className="mt-auto pb-6 pt-10">
                                <div className="mb-5 h-px bg-white/10" />

                                <div className="mb-5 flex items-center justify-between gap-2 px-2">
                                    <div className="flex min-w-0 items-center gap-2">
                                        <UserCircleIcon className="h-5 w-5 shrink-0 text-turquoise" />

                                        <span className="text-xs font-medium text-text-soft">
                                            Your session
                                        </span>
                                    </div>

                                    {isLoggedIn && (
                                        <span className="shrink-0 rounded-full border border-turquoise/20 bg-turquoise/10 px-2.5 py-1 text-[10px] font-semibold text-turquoise">
                                            Active
                                        </span>
                                    )}
                                </div>

                                <button
                                    type="button"
                                    onClick={handleLogoutClick}
                                    className="btn btn-primary btn-md flex w-full items-center justify-center gap-3 rounded-xl"
                                >
                                    <ArrowRightStartOnRectangleIcon className="h-4 w-4 shrink-0" />

                                    {isLoggedIn ? 'Logout' : 'Login'}
                                </button>
                            </div>
                        </nav>

                        <div className="relative shrink-0 border-t border-white/10 px-5 py-5 text-center">
                            <p className="text-[10px] font-medium uppercase tracking-[0.10em] text-text-muted">
                                Interactive Conference Experience
                            </p>
                        </div>
                    </div>
                </Menu>
            </div>

            <LogoutPopup
                open={logoutOpen}
                onCancel={() => setLogoutOpen(false)}
                onConfirm={handleLogoutConfirm}
            />
        </>
    );
}
