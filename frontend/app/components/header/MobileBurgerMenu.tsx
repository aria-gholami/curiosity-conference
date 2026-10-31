import { MENU_STYLES } from '~/shared/styles/menuStyles';
import { ChevronRightIcon, HomeIcon, TrophyIcon, UserCircleIcon } from '@heroicons/react/24/solid';
import { ArrowRightStartOnRectangleIcon } from '@heroicons/react/16/solid';
import { useNavigate } from 'react-router';
import type MobileBurgerMenuProps from '~/components/header/types/MobileBurgerMenuProps';
import BurgerMenu from 'react-burger-menu';

const { slide: Menu } = BurgerMenu;

export default function MobileBurgerMenu(props: MobileBurgerMenuProps) {
    const navigate = useNavigate();

    const handleNavigate = (path: string) => {
        props.setMobileOpen(false);
        navigate(path);
    };

    return (
        <div className="mobile-menu-wrapper">
            <Menu
                right
                width="min(320px, 88vw)"
                isOpen={props.mobileOpen}
                onStateChange={(state: any) => props.setMobileOpen(state.isOpen)}
                customBurgerIcon={false}
                customCrossIcon={false}
                disableAutoFocus
                styles={MENU_STYLES}
                id="mobile-menu"
            >
                <div className="mobile-menu-container">
                    <div className="mobile-menu-glow-turquoise" />
                    <div className="mobile-menu-glow-dark-blue" />

                    <div className="mobile-menu-header">
                        <div className="mobile-menu-brand">
                            <img
                                src="/logo.svg"
                                alt="Curious Activities Logo"
                                className="mobile-menu-logo"
                            />

                            <div className="mobile-menu-brand-text">
                                <p className="mobile-menu-brand-title">Curious Activities</p>

                                <p className="mobile-menu-brand-subtitle">Conference Experience</p>
                            </div>
                        </div>
                    </div>

                    <nav className="mobile-menu-nav">
                        <div className="mobile-menu-nav-heading">
                            <span className="mobile-menu-nav-indicator" />

                            <span className="mobile-menu-nav-label">Explore Activities</span>
                        </div>

                        <div className="mobile-menu-nav-items">
                            <button
                                type="button"
                                onClick={() => handleNavigate('/')}
                                className="mobile-menu-nav-item mobile-menu-nav-item-home"
                            >
                                <div className="mobile-menu-nav-icon">
                                    <HomeIcon className="mobile-menu-nav-icon-svg" />
                                </div>

                                <span className="mobile-menu-nav-item-label">Home</span>

                                <ChevronRightIcon className="mobile-menu-nav-arrow" />
                            </button>

                            <button
                                type="button"
                                onClick={() => handleNavigate('/trivia/list')}
                                className="mobile-menu-nav-item mobile-menu-nav-item-trivia"
                            >
                                <div className="mobile-menu-nav-icon">
                                    <TrophyIcon className="mobile-menu-nav-icon-svg" />
                                </div>

                                <span className="mobile-menu-nav-item-label">Trivia Challenge</span>

                                <ChevronRightIcon className="mobile-menu-nav-arrow" />
                            </button>
                        </div>

                        <div className="mobile-menu-session">
                            <div className="mobile-menu-session-divider" />

                            <div className="mobile-menu-session-info">
                                <div className="mobile-menu-session-label-container">
                                    <UserCircleIcon className="mobile-menu-session-icon" />

                                    <span className="mobile-menu-session-label">Your session</span>
                                </div>

                                {props.isLoggedIn && (
                                    <span className="mobile-menu-session-active">Active</span>
                                )}
                            </div>

                            <button
                                type="button"
                                onClick={props.handleLogoutClick}
                                className="btn btn-primary btn-md mobile-menu-auth-button"
                            >
                                <ArrowRightStartOnRectangleIcon className="mobile-menu-auth-icon" />

                                {props.isLoggedIn ? 'Logout' : 'Login'}
                            </button>
                        </div>
                    </nav>

                    <div className="mobile-menu-footer">
                        <p className="mobile-menu-footer-text">Interactive Conference Experience</p>
                    </div>
                </div>
            </Menu>
        </div>
    );
}
