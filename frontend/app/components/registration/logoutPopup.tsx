type LogoutPopupProps = {
    open: boolean;
    onCancel: () => void;
    onConfirm: () => void;
};

export default function LogoutPopup({ open, onCancel, onConfirm }: LogoutPopupProps) {
    if (!open) {
        return null;
    }

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div className="absolute inset-0 bg-dark-blue/55 backdrop-blur-md" onClick={onCancel} />

            <div className="glass relative w-full max-w-lg overflow-hidden rounded-[2rem] border border-white/20 bg-white/90">
                <div className="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-turquoise/15 blur-3xl" />
                <div className="pointer-events-none absolute -left-12 bottom-0 h-32 w-32 rounded-full bg-dark-blue/20 blur-3xl" />
                <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,0.6)_0%,transparent_55%)]" />

                <div className="relative p-6 sm:p-8">
                    <div>
                        <div className="inline-flex items-center gap-2 rounded-full border border-turquoise/20 bg-turquoise/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-dark-blue">
                            Logout
                        </div>

                        <h2 className="mt-4 text-2xl font-semibold tracking-[-0.02em] text-dark-blue sm:text-3xl">
                            Leave session?
                        </h2>

                        <p className="mt-3 text-sm leading-6 text-card-text sm:text-base">
                            Are you sure you want to log out?
                        </p>
                    </div>

                    <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                        <button
                            type="button"
                            onClick={onConfirm}
                            className="btn-primary group px-6 py-3 shadow-[0_8px_28px_rgba(230,103,39,0.3)] hover:shadow-[0_12px_36px_rgba(230,103,39,0.4)]"
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
