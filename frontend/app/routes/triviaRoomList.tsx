import { PlusIcon, ArrowRightIcon, UserGroupIcon } from '@heroicons/react/24/solid';
import { useState } from 'react';
import { useFetchQuizRoomList } from '~/routes/hooks/useFetchQuizRoomList';
import { useDisplayRegistrationPopup } from '~/routes/hooks/useDisplayRegistrationPopup';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import RegistrationPopup from '~/components/registration/registrationPopup';
import TriviaRoomCreatePopup from '~/components/trivia/triviaRoomCreatePopup';
import { useNavigate } from 'react-router';
import type { UserState } from '~/features/user/state/Initial';
import type QuizRoomJoinRequestBody from '~/features/quizRoom/state/QuizRoomJoinRequestBody';
import { joinQuizRoom } from '~/features/quizRoom/reducers/joinQuizRoom';
import { useFetchActiveQuizRoom } from '~/routes/hooks/useFetchActiveQuizRoom';

export default function TriviaRoomList() {
    const navigate = useNavigate();
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);

    const [isRegistrationPopupOpen, setIsRegistrationPopupOpen] = useState(true);
    const [isCreateRoomPopupOpen, setIsCreateRoomPopupOpen] = useState(false);

    useDisplayRegistrationPopup(setIsRegistrationPopupOpen);
    useFetchQuizRoomList();
    useFetchActiveQuizRoom();

    return (
        <div className="relative min-h-screen overflow-hidden font-sans bg-(image:--background-main)">
            <div className="absolute inset-0 bg-(image:--background-overlay)" />
            <div className="absolute -right-32 -top-32 h-96 w-96 rounded-full blur-3xl bg-turquoise-glow" />
            <div className="absolute -left-24 top-1/3 h-72 w-72 rounded-full blur-3xl bg-dark-blue-glow" />
            <div className="absolute bottom-0 right-1/4 h-64 w-64 rounded-full blur-3xl bg-light-blue-glow" />

            <RegistrationPopup open={isRegistrationPopupOpen} />
            <TriviaRoomCreatePopup
                open={isCreateRoomPopupOpen}
                onClose={() => setIsCreateRoomPopupOpen(false)}
            />

            <div className="relative z-10 mx-auto flex max-w-5xl flex-col px-6 pb-20 pt-14 sm:px-8">
                <div className="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-center">
                    <div>
                        <p className="text-text-light text-sm uppercase tracking-[0.14em]">
                            Trivia Challenge
                        </p>
                        <h1 className="mt-3 text-4xl font-semibold tracking-[-0.02em] text-white sm:text-5xl">
                            Quiz Rooms
                        </h1>
                        <p className="text-text-soft mt-3 max-w-2xl text-sm leading-6 sm:text-base">
                            Join an existing room or create a new quiz room for other conference
                            participants.
                        </p>
                    </div>

                    <button
                        type="button"
                        onClick={() => setIsCreateRoomPopupOpen(true)}
                        className="btn btn-primary btn-lg flex items-center gap-2"
                    >
                        <PlusIcon className="h-5 w-5" />
                        <span>Create New Room</span>
                    </button>
                </div>

                <div className="glass mt-10 rounded-[2rem] p-5 sm:p-6 max-h-[60vh] overflow-y-auto">
                    <div className="flex flex-col gap-4">
                        {quizRoomState.rooms.map((room) => (
                            <div
                                key={room.id}
                                className="card flex flex-col gap-4 rounded-3xl px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div className="flex items-start gap-4">
                                    <div className="icon-box relative flex h-12 w-12 items-center justify-center border border-slate-200 bg-slate-100 text-cyan-700">
                                        <UserGroupIcon className="h-5 w-5" />
                                    </div>

                                    <div>
                                        <h2 className="text-lg font-semibold text-dark-blue">
                                            {room.quiz.title}
                                        </h2>
                                        <p className="text-card-text mt-1 text-sm">
                                            {room.participants.length} players · Status:{' '}
                                            {room.status}
                                        </p>
                                    </div>
                                </div>

                                <button
                                    className="btn btn-primary btn-md flex items-center gap-2 self-start sm:self-auto"
                                    onClick={() => {
                                        const requestBody: QuizRoomJoinRequestBody = {
                                            userId: userState.userId,
                                            quizRoomId: room.id,
                                        };
                                        dispatch(joinQuizRoom(requestBody) as any);
                                        navigate('/trivia/lobby');
                                    }}
                                >
                                    <span>Join</span>
                                    <ArrowRightIcon className="h-4 w-4" />
                                </button>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
