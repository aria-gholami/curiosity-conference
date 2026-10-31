export default interface MobileBurgerMenuProps {
    setMobileOpen: (mobileOpen: boolean) => void;
    mobileOpen: boolean;
    handleLogoutClick: () => void;
    isLoggedIn: boolean;
}
