import { ArrowRightIcon, PencilSquareIcon, TrophyIcon, UsersIcon } from '@heroicons/react/24/solid';
import RegistrationPopup from '~/components/registration/registrationPopup';
import { useState } from 'react';
import { useDisplayRegistrationPopup } from '~/routes/hooks/useDisplayRegistrationPopup';
import { useNavigate } from 'react-router';

export default function Home() {
    const [isRegistrationPopupOpen, setIsRegistrationPopupOpen] = useState(true);
    const navigate = useNavigate();

    const featureCards = [
        {
            title: 'Networking',
            text: 'Join fun icebreakers and mini-games to connect with other scientific minds.',
            cta: 'Open activity',
            icon: UsersIcon,
            glowClass: 'bg-turquoise-glow',
            iconBgClass: 'bg-indigo-50',
            iconColorClass: 'text-indigo-600',
            iconBorderClass: 'border-slate-200',
            link: '/trivia/list',
        },
        {
            title: 'Trivia Challenge',
            text: 'Test your knowledge with science-themed trivia and compete for the top score.',
            cta: 'Open activity',
            icon: TrophyIcon,
            glowClass: 'bg-accent-orange/15',
            iconBgClass: 'bg-orange-50',
            iconColorClass: 'text-accent-orange',
            iconBorderClass: 'border-orange-200',
            link: '/trivia/list',
        },
        {
            title: 'Creative Corner',
            text: 'Express your creativity through interactive writing prompts and playful tasks.',
            cta: 'Open activity',
            icon: PencilSquareIcon,
            glowClass: 'bg-light-blue-glow',
            iconBgClass: 'bg-slate-100',
            iconColorClass: 'text-cyan-700',
            iconBorderClass: 'border-slate-200',
            link: '/trivia/list',
        },
    ];

    useDisplayRegistrationPopup(setIsRegistrationPopupOpen);

    return (
        <div className="relative min-h-screen overflow-hidden font-sans bg-(image:--background-main)">
            <div className="absolute inset-0 bg-(image:--background-overlay)" />
            <div className="absolute -right-32 -top-32 h-96 w-96 rounded-full blur-3xl bg-turquoise-glow" />
            <div className="absolute -left-24 top-1/3 h-72 w-72 rounded-full blur-3xl bg-dark-blue-glow" />
            <div className="absolute bottom-0 right-1/4 h-64 w-64 rounded-full blur-3xl bg-light-blue-glow" />
            <RegistrationPopup open={isRegistrationPopupOpen} />

            <div className="relative mx-auto flex max-w-5xl flex-col items-center px-6 pb-20 pt-14 text-center sm:px-8">
                <div className="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 shadow-sm backdrop-blur-sm">
                    <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-turquoise" />
                    <span className="text-text-light text-xs font-semibold uppercase tracking-[0.14em]">
                        Interactive Conference Experience
                    </span>
                </div>

                <h1 className="mt-8 text-6xl font-semibold tracking-[-0.02em] text-white sm:text-7xl">
                    Curious <span className="text-accent-orange italic">Activities</span>
                </h1>

                <p className="text-text-soft mt-5 max-w-xl leading-7 sm:text-lg">
                    Interactive activities and games for scientific conference attendees. Meet
                    people, play, and explore ideas together.
                </p>

                <div className="mt-9 flex w-full flex-col items-center justify-center gap-3 sm:w-auto sm:flex-row">
                    <button className="btn btn-primary btn-lg group gap-2.5 overflow-hidden shadow-[0_8px_32px_rgba(230,103,39,0.28)] hover:shadow-[0_12px_40px_rgba(230,103,39,0.38)] active:translate-y-0">
                        <span>Explore activities</span>
                        <ArrowRightIcon className="h-3.5 w-3.5" />
                    </button>

                    <button className="btn btn-secondary btn-lg">Learn more</button>
                </div>

                <p className="text-text-muted mt-10 flex items-center gap-2 text-xs uppercase tracking-widest">
                    <span className="h-px w-8 bg-white/20" />
                    Connect with fellow conference participants
                    <span className="h-px w-8 bg-white/20" />
                </p>

                <div className="glass mt-7 flex w-full flex-col rounded-[2.5rem] p-5 sm:p-6">
                    <div className="flex flex-col gap-4 md:flex-row md:items-stretch">
                        {featureCards.map((card) => {
                            const Icon = card.icon;

                            return (
                                <article
                                    key={card.title}
                                    className="card group min-h-[280px] flex-1 px-6 py-7 text-left"
                                >
                                    <div className="pointer-events-none absolute inset-0 rounded-[inherit] bg-[linear-gradient(135deg,rgba(255,255,255,0.6)_0%,transparent_60%)]" />

                                    <div
                                        className={`pointer-events-none absolute -right-6 -top-6 h-28 w-28 rounded-full blur-2xl ${card.glowClass}`}
                                    />

                                    <div
                                        className={`icon-box relative mb-5 h-12 w-12 ${card.iconBgClass} ${card.iconColorClass} ${card.iconBorderClass}`}
                                    >
                                        <Icon className="h-5 w-5" />
                                    </div>

                                    <h2 className="relative text-base font-semibold text-dark-blue">
                                        {card.title}
                                    </h2>

                                    <p className="text-card-text relative mt-2.5 flex-1 text-sm leading-6">
                                        {card.text}
                                    </p>

                                    <button
                                        className="btn btn-primary btn-md relative mt-6 w-fit gap-2"
                                        onClick={() => navigate(card.link)}
                                    >
                                        {card.cta}
                                        <ArrowRightIcon className="h-3 w-3" />
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
