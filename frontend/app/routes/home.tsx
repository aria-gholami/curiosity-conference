import { ArrowRightIcon, TrophyIcon, UsersIcon } from '@heroicons/react/24/solid';
import RegistrationPopup from '~/components/registration/registrationPopup';
import { useState } from 'react';
import { useDisplayRegistrationPopup } from '~/routes/hooks/useDisplayRegistrationPopup';
import { useNavigate } from 'react-router';
import { useFetchActiveQuizRoom } from '~/routes/hooks/useFetchActiveQuizRoom';

export default function Home() {
    const [isRegistrationPopupOpen, setIsRegistrationPopupOpen] = useState(true);
    const navigate = useNavigate();

    const featureCards = [
        {
            title: 'Networking',
            text: 'Join fun icebreakers and mini-games to connect with other scientific minds.',
            cta: 'Open activity',
            icon: UsersIcon,
            variant: 'networking',
            link: '/trivia/list',
        },
        {
            title: 'Trivia Challenge',
            text: 'Test your knowledge with science-themed trivia and compete for the top score.',
            cta: 'Open activity',
            icon: TrophyIcon,
            variant: 'trivia',
            link: '/trivia/list',
        },
    ];

    useDisplayRegistrationPopup(setIsRegistrationPopupOpen);
    useFetchActiveQuizRoom();

    return (
        <div className="page-background">
            <div className="page-background-overlay" />
            <div className="page-glow-turquoise" />
            <div className="page-glow-dark-blue" />
            <div className="page-glow-light-blue" />

            <RegistrationPopup open={isRegistrationPopupOpen} />

            <div className="home-container">
                <div className="home-badge">
                    <span className="home-badge-indicator" />
                    <span className="home-badge-text">Interactive Conference Experience</span>
                </div>

                <h1 className="home-title">
                    Curious <span className="home-title-accent">Activities</span>
                </h1>

                <p className="home-description">
                    Interactive activities and games for scientific conference attendees. Meet
                    people, play, and explore ideas together.
                </p>

                <p className="home-section-label">
                    <span className="home-section-divider" />
                    Connect with fellow conference participants
                    <span className="home-section-divider" />
                </p>

                <div className="glass home-features">
                    <div className="home-features-grid">
                        {featureCards.map((card) => {
                            const Icon = card.icon;

                            return (
                                <article
                                    key={card.title}
                                    className={`card home-feature-card home-feature-card-${card.variant}`}
                                >
                                    <div className="home-feature-gradient" />
                                    <div className="home-feature-glow" />
                                    <div className="icon-box home-feature-icon-box">
                                        <Icon className="home-feature-icon" />
                                    </div>
                                    <h2 className="home-feature-title">{card.title}</h2>
                                    <p className="home-feature-description">{card.text}</p>

                                    <button
                                        type="button"
                                        className="btn btn-primary btn-md home-feature-button"
                                        onClick={() => navigate(card.link)}
                                    >
                                        {card.cta}
                                        <ArrowRightIcon className="home-feature-button-icon" />
                                    </button>
                                </article>
                            );
                        })}
                    </div>
                </div>
            </div>
        </div>
    );
}
