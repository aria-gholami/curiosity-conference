import RegistrationPopup from '~/components/registration/registrationPopup';
import { useState } from 'react';
import { useDisplayRegistrationPopup } from '~/routes/hooks/useDisplayRegistrationPopup';
import { useFetchActiveQuizRoom } from '~/routes/hooks/useFetchActiveQuizRoom';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useNavigate } from 'react-router';
import { TrophyIcon, ArrowRightIcon } from '@heroicons/react/24/solid';
import { resetCurrentRoom, resetRooms } from '~/features/quizRoom/quizRoomSlice';
import { closeQuizRoom } from '~/features/quizRoom/reducers/closeQuizRoom';
import { useFetchHighscoreByQuizRoom } from '~/routes/hooks/useFetchHighscoreByQuizRoom';

export default function TriviaHighscore() {
    const [isRegistrationPopupOpen, setIsRegistrationPopupOpen] = useState(true);
    const navigate = useNavigate();
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);

    useDisplayRegistrationPopup(setIsRegistrationPopupOpen);
    useFetchActiveQuizRoom();
    useFetchHighscoreByQuizRoom();

    const highscores = [...quizRoomState.currentRoom.participants].sort(
        (a, b) => (b.score ?? 0) - (a.score ?? 0),
    );

    const handleLeaveToRoomList = () => {
        dispatch(closeQuizRoom(quizRoomState.currentRoom.id));
        dispatch(resetRooms());
        dispatch(resetCurrentRoom());
        navigate('/trivia/list');
    };

    return (
        <div className="relative min-h-screen overflow-hidden font-sans bg-(image:--background-main)">
            <div className="absolute inset-0 bg-(image:--background-overlay)" />
            <div className="absolute -right-32 -top-32 h-96 w-96 rounded-full blur-3xl bg-turquoise-glow" />
            <div className="absolute -left-24 top-1/3 h-72 w-72 rounded-full blur-3xl bg-dark-blue-glow" />
            <div className="absolute bottom-0 right-1/4 h-64 w-64 rounded-full blur-3xl bg-light-blue-glow" />

            <RegistrationPopup open={isRegistrationPopupOpen} />

            <div className="relative z-10 mx-auto flex max-w-5xl flex-col px-6 pb-20 pt-14 sm:px-8">
                <div className="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="text-text-light text-sm uppercase tracking-[0.14em] flex items-center gap-1.5">
                            Trivia Challenge
                        </p>
                        <h1 className="mt-3 text-4xl font-semibold tracking-[-0.02em] text-white sm:text-5xl">
                            Highscore
                        </h1>
                        <p className="text-text-soft mt-3 max-w-2xl text-sm leading-6 sm:text-base">
                            Final results and scores for{' '}
                            {quizRoomState.currentRoom.title
                                ? `"${quizRoomState.currentRoom.title}"`
                                : 'all participants in the same quiz room.'}
                            .
                        </p>
                    </div>

                    <button
                        type="button"
                        onClick={handleLeaveToRoomList}
                        className="btn btn-primary btn-md flex items-center gap-2 self-start sm:self-auto"
                    >
                        <span>Room List</span>
                        <ArrowRightIcon className="h-4 w-4" />
                    </button>
                </div>

                <div className="mt-10 flex justify-center">
                    <div className="glass w-full max-w-3xl rounded-[2rem] p-5 sm:p-6">
                        <div className="card rounded-3xl px-6 py-6">
                            <div className="flex items-center justify-between border-b border-slate-200 pb-5">
                                <div className="flex items-center gap-3">
                                    <div className="icon-box relative flex h-12 w-12 items-center justify-center border border-amber-200 bg-amber-50 text-amber-500">
                                        <TrophyIcon className="h-6 w-6" />
                                    </div>
                                    <div>
                                        <h2 className="text-xl font-semibold text-dark-blue">
                                            Participants & Scores
                                        </h2>
                                        <p className="text-card-text text-xs">
                                            Ranked by points scored
                                        </p>
                                    </div>
                                </div>
                                <div className="rounded-full bg-dark-blue px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-white">
                                    {highscores.length} Players
                                </div>
                            </div>

                            {quizRoomState.isLoading ? (
                                <div className="py-12 text-center text-card-text">
                                    <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent motion-reduce:animate-[spin_1.5s_linear_infinite]" />
                                    <p className="mt-3 text-sm">Loading highscores...</p>
                                </div>
                            ) : highscores.length === 0 ? (
                                <div className="py-12 text-center text-card-text">
                                    <p className="text-base font-medium">No scores recorded yet.</p>
                                </div>
                            ) : (
                                <div className="mt-6 flex flex-col gap-3 max-h-[60vh] overflow-y-auto">
                                    {highscores.map((item, index) => {
                                        const isFirst = index === 0;
                                        const isSecond = index === 1;
                                        const isThird = index === 2;

                                        return (
                                            <div
                                                key={`${item.userName}-${index}`}
                                                className={`flex items-center justify-between rounded-2xl border px-5 py-4 transition-all ${
                                                    isFirst
                                                        ? 'border-amber-300 bg-amber-50/80 shadow-sm'
                                                        : isSecond
                                                          ? 'border-slate-300 bg-slate-100/80'
                                                          : isThird
                                                            ? 'border-orange-200 bg-orange-50/50'
                                                            : 'border-slate-200 bg-slate-50'
                                                }`}
                                            >
                                                <div className="flex items-center gap-4">
                                                    <div
                                                        className={`flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold ${
                                                            isFirst
                                                                ? 'bg-amber-400 text-white shadow-sm'
                                                                : isSecond
                                                                  ? 'bg-slate-400 text-white'
                                                                  : isThird
                                                                    ? 'bg-amber-700 text-white'
                                                                    : 'bg-dark-blue/10 text-dark-blue'
                                                        }`}
                                                    >
                                                        {index + 1}
                                                    </div>

                                                    <div>
                                                        <p className="font-semibold text-dark-blue">
                                                            {item.userName}
                                                        </p>
                                                        {isFirst && (
                                                            <p className="text-xs font-semibold text-amber-600">
                                                                Winner 🏆
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>

                                                <div className="text-right">
                                                    <span className="text-xl font-bold text-dark-blue">
                                                        {item.score}
                                                    </span>
                                                    <span className="ml-1 text-xs text-card-text">
                                                        pts
                                                    </span>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
