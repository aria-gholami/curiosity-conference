import { SparklesIcon } from '@heroicons/react/24/solid';

export default function Footer() {
    const links = [
        { label: 'Privacy Data Policy', href: '#' },
        { label: 'Imprint', href: '#' },
    ];

    return (
        <footer className="footer">
            <div className="footer-container">
                <div className="footer-brand">
                    <SparklesIcon className="footer-brand-icon" />
                    <span className="footer-copyright">© 2027 Curious Activities</span>
                </div>

                <div className="footer-divider" />

                <nav className="footer-navigation" aria-label="Footer navigation">
                    <ul className="footer-links" role="list">
                        {links.map((link, i) => (
                            <li key={link.label} className="footer-link-item">
                                <a href={link.href} className="footer-link">
                                    {link.label}
                                </a>

                                {i < links.length - 1 && <span className="footer-link-divider" />}
                            </li>
                        ))}
                    </ul>
                </nav>
            </div>
        </footer>
    );
}
