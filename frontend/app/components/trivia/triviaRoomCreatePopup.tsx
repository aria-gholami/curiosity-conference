import { ChevronDownIcon, PlusIcon, UserGroupIcon } from '@heroicons/react/24/solid';
import { useState } from 'react';
import { useFetchQuizList } from '~/components/trivia/hooks/useFetchQuizList';
import type { QuizState } from '~/features/quiz/state/Initial';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { createQuizRoom } from '~/features/quizRoom/reducers/createQuizRoom';
import type { QuizRoomCreateRequestBody } from '~/features/quizRoom/state/QuizRoomCreateRequestBody';
import type { UserState } from '~/features/user/state/Initial';
import { useNavigate } from 'react-router';
import { useBodyScrollLock } from '~/hooks/useBodyScrollLock';

type TriviaRoomCreatePopupProps = {
    open: boolean;
    onClose: () => void;
};

export default function TriviaRoomCreatePopup({ open, onClose }: TriviaRoomCreatePopupProps) {
    const navigate = useNavigate();
    const dispatch = useAppDispatch();
    const [selectedQuizId, setSelectedQuizId] = useState('');
    const [quizRoomTitle, setQuizRoomTitle] = useState('');
    const [errorMessage, setErrorMessage] = useState('');
    const quizState: QuizState = useAppSelector((state) => state.quiz);
    const userState: UserState = useAppSelector((state) => state.user);
    const MAX_ROOM_TITLE_LENGTH = 15;
    const selectedQuiz = quizState.quizzes.find((quiz) => quiz.id === selectedQuizId);

    useFetchQuizList();
    useBodyScrollLock(open);
    if (!open) {
        return null;
    }

    const handleSubmit = async (event: React.FormEvent) => {
        event.preventDefault();
        if (quizRoomTitle.length === 0) {
            setErrorMessage("The room title can't be empty.");
            return;
        }

        if (quizRoomTitle.length > MAX_ROOM_TITLE_LENGTH) {
            setErrorMessage('The room title must not exceed 15 characters.');
            return;
        }

        if (!selectedQuizId) {
            return;
        }

        const requestBody: QuizRoomCreateRequestBody = {
            hostId: userState.userId,
            quizId: selectedQuizId,
            title: quizRoomTitle,
        };

        await dispatch(createQuizRoom(requestBody) as any);
        navigate('/trivia/lobby');
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div className="absolute inset-0 bg-dark-blue/55 backdrop-blur-md" />

            <div className="glass relative w-full max-w-lg overflow-hidden rounded-[2rem] border border-white/20 bg-white/90">
                <div className="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-turquoise/15 blur-3xl" />
                <div className="pointer-events-none absolute -left-12 bottom-0 h-32 w-32 rounded-full bg-dark-blue/20 blur-3xl" />
                <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,0.6)_0%,transparent_55%)]" />

                <form onSubmit={handleSubmit} className="relative p-6 sm:p-8">
                    <div className="flex items-start justify-between gap-4">
                        <div>
                            <div className="inline-flex items-center gap-2 rounded-full border border-turquoise/20 bg-turquoise/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-dark-blue">
                                Trivia Challenge
                            </div>

                            <h2 className="mt-4 text-2xl font-semibold tracking-[-0.02em] text-dark-blue sm:text-3xl">
                                Create Trivia Room
                            </h2>

                            <p className="mt-3 text-sm leading-6 text-card-text sm:text-base">
                                Create a new room for the selected quiz and invite other
                                participants to join.
                            </p>
                        </div>
                    </div>

                    <div className="mt-3 space-y-4">
                        <div>
                            <label htmlFor="roomTitle" className="input-label">
                                Title
                            </label>

                            <input
                                id="roomTitle"
                                type="text"
                                placeholder="Room title"
                                className="input"
                                maxLength={MAX_ROOM_TITLE_LENGTH}
                                value={quizRoomTitle}
                                required
                                onChange={(event) => {
                                    setQuizRoomTitle(event.target.value);
                                    setErrorMessage('');
                                }}
                            />
                            <div className="mt-2 flex items-start justify-between gap-3">
                                <div className="min-w-0 flex-1">
                                    {errorMessage && (
                                        <p className="text-xs leading-5 text-red-600 mb-2">
                                            {errorMessage}
                                        </p>
                                    )}
                                </div>
                                <span
                                    className={`shrink-0 text-xs leading-3 ${
                                        quizRoomTitle.length > MAX_ROOM_TITLE_LENGTH
                                            ? 'font-semibold text-red-600'
                                            : 'text-card-text'
                                    }`}
                                >
                                    {quizRoomTitle.length}/{MAX_ROOM_TITLE_LENGTH}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div className="space-y-1">
                        <div>
                            <label htmlFor="quizSelect" className="input-label">
                                Select Quiz
                            </label>

                            <div className="relative">
                                <div className="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-cyan-700">
                                    <UserGroupIcon className="h-5 w-5" />
                                </div>

                                <select
                                    id="quizSelect"
                                    value={selectedQuizId}
                                    onChange={(e) => setSelectedQuizId(e.target.value)}
                                    className="input w-full appearance-none pl-12 pr-12"
                                >
                                    <option value="" disabled className="bg-white text-slate-500">
                                        Choose a quiz
                                    </option>

                                    {quizState.quizzes.map((quiz) => (
                                        <option
                                            key={quiz.id}
                                            value={quiz.id}
                                            className="bg-white text-black"
                                        >
                                            {quiz.title}
                                        </option>
                                    ))}
                                </select>

                                <div className="pointer-events-none absolute inset-y-0 right-4 flex items-center">
                                    <ChevronDownIcon className="h-5 w-5 text-slate-500" />
                                </div>
                            </div>

                            {selectedQuiz && (
                                <p className="mt-2 text-sm text-card-text">
                                    Selected quiz:
                                    <span className="ml-1 font-medium text-dark-blue">
                                        {selectedQuiz.title}
                                    </span>
                                </p>
                            )}
                        </div>
                    </div>

                    <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                        <button
                            type="submit"
                            disabled={!selectedQuizId || !quizRoomTitle}
                            className="btn-primary group gap-2.5 px-6 py-3 shadow-[0_8px_28px_rgba(230,103,39,0.3)] hover:shadow-[0_12px_36px_rgba(230,103,39,0.4)] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span>Create Room</span>
                            <PlusIcon className="h-4 w-4" />
                        </button>

                        <button
                            type="button"
                            onClick={onClose}
                            className="btn-secondary-light btn-lg"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
