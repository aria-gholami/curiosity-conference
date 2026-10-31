export const MENU_STYLES = {
    bmMenuWrap: {
        position: 'fixed' as const,
        top: '0',
        right: '0',
        height: '100dvh',
        maxWidth: '100vw',
        overflow: 'hidden',
    },
    bmMenu: {
        background: '#0c1830',
        overflowX: 'hidden' as const,
        overflowY: 'auto' as const,
    },
    bmOverlay: {
        background: 'rgba(3, 9, 23, 0.65)',
        backdropFilter: 'blur(5px)',
        top: '0',
    },
    bmItemList: {
        height: '100%',
    },
    bmItem: {
        outline: 'none',
    },
};
