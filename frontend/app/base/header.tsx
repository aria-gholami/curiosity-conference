import { SparklesIcon, Bars3Icon, XMarkIcon } from '@heroicons/react/24/solid';
import { useState } from 'react';
import type { UserState } from '~/features/user/state/Initial';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { deleteUser } from '~/features/user/reducers/deleteUser';
import type { UserDeleteRequestBody } from '~/features/user/state/UserDeleteRequestBody';
import { resetCurrentRoom, resetRooms } from '~/features/quizRoom/quizRoomSlice';

export default function Header() {
    const dispatch = useAppDispatch();
    const userState: UserState = useAppSelector((state) => state.user);
    const [mobileOpen, setMobileOpen] = useState(false);

    const isLoggedIn = userState.userId !== '';

    const handleLogout = () => {
        if (!isLoggedIn) {
            return;
        }

        const userDeleteRequestBody: UserDeleteRequestBody = {
            userId: userState.userId,
        };

        dispatch(deleteUser(userDeleteRequestBody) as any);
        dispatch(resetCurrentRoom() as any);
        dispatch(resetRooms() as any);
        setMobileOpen(false);
    };

    return (
        <header className="sticky top-0 z-50 border-b border-white/55 bg-white/80 backdrop-blur-[20px]">
            <div className="mx-auto flex max-w-6xl items-center justify-between px-6 py-3.5 sm:px-8">
                <a href="/" className="group flex items-center gap-2.5">
                    <div className="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-sky-200 to-violet-200 shadow-sm ring-1 ring-white/90 transition duration-300 group-hover:scale-105 group-hover:shadow-md">
                        <SparklesIcon className="h-4 w-4 text-indigo-500 transition-transform duration-300 group-hover:rotate-6" />
                    </div>
                    <span className="text-sm font-semibold tracking-[0.06em] text-slate-800">
                        Curious Activities
                    </span>
                </a>

                <div className="hidden md:block">
                    <button
                        type="button"
                        className="btn-primary btn-sm select-none"
                        onClick={handleLogout}
                    >
                        {isLoggedIn ? 'Logout' : 'Login'}
                    </button>
                </div>

                <button
                    className="relative flex h-9 w-9 items-center justify-center rounded-xl border border-white/70 bg-white/60 text-slate-500 shadow-sm backdrop-blur-sm transition-all duration-200 hover:bg-white active:scale-95 md:hidden"
                    onClick={() => setMobileOpen((v) => !v)}
                    aria-label="Toggle menu"
                >
                    <Bars3Icon
                        className={`absolute h-4 w-4 transition-all duration-300 ${
                            mobileOpen
                                ? 'rotate-90 scale-75 opacity-0'
                                : 'rotate-0 scale-100 opacity-100'
                        }`}
                    />
                    <XMarkIcon
                        className={`absolute h-4 w-4 transition-all duration-300 ${
                            mobileOpen
                                ? 'rotate-0 scale-100 opacity-100'
                                : '-rotate-90 scale-75 opacity-0'
                        }`}
                    />
                </button>
            </div>

            <div
                className={`overflow-hidden border-t border-white/40 bg-white/95 transition-all duration-300 ease-out md:hidden ${
                    mobileOpen
                        ? 'max-h-96 translate-y-0 opacity-100'
                        : 'max-h-0 -translate-y-2 opacity-0'
                }`}
            >
                <div className="px-6 pb-5 pt-4">
                    <nav className="flex flex-col">
                        <button
                            type="button"
                            className={`btn-primary btn-md mt-3 text-center select-none ${
                                mobileOpen ? 'translate-y-0 opacity-100' : 'translate-y-2 opacity-0'
                            }`}
                            onClick={handleLogout}
                        >
                            {isLoggedIn ? 'Logout' : 'Login'}
                        </button>
                    </nav>
                </div>
            </div>
        </header>
    );
}
