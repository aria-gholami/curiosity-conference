import { SparklesIcon } from '@heroicons/react/24/solid';

export default function Footer() {
    const links = [
        { label: 'Privacy Data Policy', href: '#' },
        { label: 'Imprint', href: '#' },
    ];

    return (
        <footer className="border-t border-slate-300/60 bg-white/70 font-sans shadow-[inset_0_1px_0_rgba(255,255,255,0.9)] backdrop-blur-[20px]">
            <div className="mx-auto flex max-w-6xl flex-col items-center gap-4 px-6 py-5 sm:px-8 md:flex-row md:justify-between">
                <div className="flex items-center gap-2">
                    <SparklesIcon className="h-3.5 w-3.5 text-indigo-400" />
                    <span className="text-sm font-medium text-slate-600">
                        © 2026 Curious Activities
                    </span>
                </div>

                <div className="h-px w-full bg-gradient-to-r from-transparent via-slate-200 to-transparent md:hidden" />

                <nav aria-label="Footer navigation">
                    <ul className="flex items-center gap-1" role="list">
                        {links.map((link, i) => (
                            <li key={link.label} className="flex items-center">
                                <a
                                    href={link.href}
                                    className="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 underline-offset-2 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                                >
                                    {link.label}
                                </a>

                                {i < links.length - 1 && (
                                    <span className="h-3.5 w-px bg-slate-300" />
                                )}
                            </li>
                        ))}
                    </ul>
                </nav>
            </div>
        </footer>
    );
}
