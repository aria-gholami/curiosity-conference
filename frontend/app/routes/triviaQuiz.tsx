import RegistrationPopup from '~/components/registration/registrationPopup';
import { useState } from 'react';
import { useDisplayRegistrationPopup } from '~/routes/hooks/useDisplayRegistrationPopup';
import { useFetchActiveQuizRoom } from '~/routes/hooks/useFetchActiveQuizRoom';
import { useLobbyNotFoundRedirect } from '~/routes/hooks/useLobbyNotFoundRedirect';
import { useAppSelector } from '~/redux/hooks';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import type { UserState } from '~/features/user/state/Initial';
import { useNotStartedQuiz } from '~/routes/hooks/useNotStartedQuiz';
import { useFetchQuizRoomQuestion } from '~/routes/hooks/useFetchQuizRoomQuestion';
import { useAppDispatch } from '~/redux/hooks';
import { deleteQuizRoom } from '~/features/quizRoom/reducers/deleteQuizRoom';
import { deleteParticipant } from '~/features/quizRoom/reducers/deleteParticipant';
import { useNavigate } from 'react-router';
import { setSelectedAnswerId } from '~/features/quizRoom/quizRoomSlice';
import { revealAnswer } from '~/features/quizRoom/reducers/revealAnswer';

export default function TriviaQuiz() {
    const dispatch = useAppDispatch();
    const navigate = useNavigate();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);
    const [isRegistrationPopupOpen, setIsRegistrationPopupOpen] = useState(true);
    const [currentQuestion, setCurrentQuestion] = useState({
        question: 'Loading...',
        answers: [
            { id: '1', title: 'Answer A', isCorrect: true },
            { id: '2', title: 'Answer B', isCorrect: false },
            { id: '3', title: 'Answer C', isCorrect: false },
        ],
    });

    const selectedAnswerId = quizRoomState.currentRoom.selectedAnswerId;

    useDisplayRegistrationPopup(setIsRegistrationPopupOpen);
    useFetchActiveQuizRoom();
    useLobbyNotFoundRedirect();
    useNotStartedQuiz();
    useFetchQuizRoomQuestion(setCurrentQuestion);

    const isHost = userState.userId === quizRoomState.currentRoom.host.id;

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
                        <p className="text-text-light text-sm uppercase tracking-[0.14em]">
                            {quizRoomState.currentRoom.quiz.title || 'Trivia Challenge'}
                        </p>
                        <h1 className="mt-3 text-4xl font-semibold tracking-[-0.02em] text-white sm:text-5xl">
                            Quiz Room
                        </h1>
                    </div>
                    <div className="rounded-full bg-white/10 px-4 py-2 backdrop-blur-md border border-white/20">
                        <span className="text-white font-medium">
                            Room: {quizRoomState.currentRoom.title}
                        </span>
                    </div>
                </div>

                <div className="mt-12 flex flex-col items-center">
                    <div className="glass w-full max-w-4xl rounded-[2.5rem] p-6 sm:p-8">
                        <div className="card min-h-[200px] flex flex-col items-center justify-center rounded-3xl px-8 py-10 text-center">
                            <h2 className="text-2xl font-bold text-dark-blue sm:text-3xl leading-snug">
                                {currentQuestion.question}
                            </h2>
                        </div>

                        <div className="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            {currentQuestion.answers.map((answer, index) => {
                                return (
                                    <button
                                        key={answer.id}
                                        type="button"
                                        onClick={() =>
                                            !quizRoomState.isRevealing &&
                                            dispatch(setSelectedAnswerId(answer.id))
                                        }
                                        className={`group relative flex items-center gap-4 rounded-2xl border-2 px-6 py-5 text-left transition-all duration-200 cursor-pointer ${
                                            quizRoomState.isRevealing && answer.isCorrect
                                                ? 'reveal-correct z-20 scale-[1.05]'
                                                : selectedAnswerId === answer.id
                                                  ? 'border-dark-blue bg-dark-blue text-white shadow-lg scale-[1.02]'
                                                  : `border-transparent text-dark-blue hover:border-turquoise bg-slate-50 hover:shadow-md`
                                        } ${quizRoomState.isRevealing && !answer.isCorrect ? 'opacity-50 grayscale-[0.5]' : ''}`}
                                        disabled={quizRoomState.isRevealing}
                                    >
                                        <div
                                            className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-lg font-bold transition-colors ${
                                                selectedAnswerId === answer.id
                                                    ? 'bg-white text-dark-blue'
                                                    : 'bg-dark-blue text-white group-hover:bg-turquoise'
                                            }`}
                                        >
                                            {String.fromCharCode(65 + index)}
                                        </div>
                                        <span className="text-lg font-semibold">
                                            {answer.title}
                                        </span>
                                    </button>
                                );
                            })}
                        </div>

                        {isHost ? (
                            <div className="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                                <button
                                    type="button"
                                    disabled={quizRoomState.isRevealing}
                                    onClick={() => {
                                        dispatch(revealAnswer(quizRoomState.currentRoom.id));
                                    }}
                                    className={`btn-primary px-10 py-4 text-lg rounded-2xl shadow-xl hover:scale-105 transition-transform ${quizRoomState.isRevealing ? 'opacity-50 cursor-not-allowed' : ''}`}
                                >
                                    Next Question
                                </button>
                                <button
                                    type="button"
                                    onClick={() => {
                                        dispatch(
                                            deleteQuizRoom({
                                                quizRoomId: quizRoomState.currentRoom.id,
                                            }),
                                        ).then(() => navigate('/'));
                                    }}
                                    className="px-10 py-4 text-lg font-bold rounded-2xl border-2 border-red-500/50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-lg hover:scale-105 cursor-pointer"
                                >
                                    Delete Room
                                </button>
                            </div>
                        ) : (
                            <div className="mt-10 flex justify-center">
                                <button
                                    type="button"
                                    onClick={() => {
                                        dispatch(
                                            deleteParticipant({
                                                quizRoomId: quizRoomState.currentRoom.id,
                                                participantId: userState.userId,
                                            }),
                                        ).then(() => navigate('/'));
                                    }}
                                    className="px-10 py-4 text-lg font-bold rounded-2xl border-2 border-white/20 text-white/70 hover:bg-white/10 hover:text-white transition-all shadow-lg hover:scale-105 cursor-pointer"
                                >
                                    Leave Room
                                </button>
                            </div>
                        )}
                    </div>

                    <div className="mt-8 flex gap-4 overflow-x-auto pb-4 w-full justify-center">
                        {quizRoomState.currentRoom.participants.map((participant) => (
                            <div
                                key={participant.userId}
                                className="flex flex-col items-center gap-2 min-w-[80px]"
                            >
                                <div className="relative">
                                    <div className="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white font-bold shadow-sm">
                                        {participant.userName.slice(0, 1).toUpperCase()}
                                    </div>
                                    <div className="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-green-500 border-2 border-slate-900" />
                                </div>
                                <span className="text-xs text-white/80 font-medium truncate max-w-[80px]">
                                    {participant.userName}
                                </span>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
