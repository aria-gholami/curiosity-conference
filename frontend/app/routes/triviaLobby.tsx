import { ArrowRightIcon, PlayIcon } from '@heroicons/react/24/solid';
import { useState } from 'react';
import { useDisplayRegistrationPopup } from '~/routes/hooks/useDisplayRegistrationPopup';
import RegistrationPopup from '~/components/registration/registrationPopup';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { useFetchActiveQuizRoom } from '~/routes/hooks/useFetchActiveQuizRoom';
import { useLobbyNotFoundRedirect } from '~/routes/hooks/useLobbyNotFoundRedirect';
import type QuizRoomStartRequestBody from '~/features/quizRoom/state/QuizRoomStartRequestBody';
import { startQuizRoom } from '~/features/quizRoom/reducers/startQuizRoom';
import type QuizRoomDeleteParticipantAttributes from '~/features/quizRoom/state/QuizRoomDeleteParticipantAttributes';
import type { UserState } from '~/features/user/state/Initial';
import { deleteParticipant } from '~/features/quizRoom/reducers/deleteParticipant';
import { deleteQuizRoom } from '~/features/quizRoom/reducers/deleteQuizRoom';
import type QuizRoomDeleteParams from '~/features/quizRoom/state/QuizRoomDeleteRequestBody';
import { resetCurrentRoom, resetRooms } from '~/features/quizRoom/quizRoomSlice';

export default function TriviaLobby() {
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);
    const [isRegistrationPopupOpen, setIsRegistrationPopupOpen] = useState(true);

    useDisplayRegistrationPopup(setIsRegistrationPopupOpen);
    useFetchActiveQuizRoom();
    useLobbyNotFoundRedirect();

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
                            Trivia Challenge
                        </p>
                        <h1 className="mt-3 text-4xl font-semibold tracking-[-0.02em] text-white sm:text-5xl">
                            Lobby Room
                        </h1>
                        <p className="text-text-soft mt-3 max-w-2xl text-sm leading-6 sm:text-base">
                            Wait for other participants to join, share the room code, and start the
                            game when everyone is ready.
                        </p>
                    </div>
                </div>

                <div className="mt-10 flex justify-center">
                    <div className="glass w-full max-w-3xl rounded-[2rem] p-5 sm:p-6">
                        <div className="card rounded-3xl px-6 py-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <h3 className="text-xl font-semibold text-dark-blue">
                                        Participants
                                    </h3>
                                    <p className="text-card-text mt-1 text-sm">
                                        Everyone currently in the lobby
                                    </p>
                                </div>

                                <div className="rounded-full bg-dark-blue px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-white">
                                    {quizRoomState.currentRoom.participants.length} joined
                                </div>
                            </div>

                            <div className="mt-6 flex flex-col gap-3 max-h-[60vh] overflow-y-auto">
                                {quizRoomState.currentRoom.participants.map((participant) => (
                                    <div
                                        key={participant.userId}
                                        className="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4"
                                    >
                                        <div className="flex items-center gap-3">
                                            <div className="flex h-11 w-11 items-center justify-center rounded-full bg-dark-blue text-sm font-semibold text-white">
                                                {participant.userName.slice(0, 1).toUpperCase()}
                                            </div>

                                            <div>
                                                <p className="font-semibold text-dark-blue">
                                                    {participant.userName}
                                                </p>
                                                <p className="text-card-text text-sm">
                                                    {participant.userId ===
                                                    quizRoomState.currentRoom.host.id
                                                        ? 'Host'
                                                        : 'Player'}
                                                </p>
                                            </div>
                                        </div>

                                        {participant.userId ===
                                            quizRoomState.currentRoom.host.id && (
                                            <span className="rounded-full bg-turquoise/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-dark-blue">
                                                Host
                                            </span>
                                        )}
                                    </div>
                                ))}
                            </div>

                            <div className="mt-8 flex flex-col gap-3">
                                {isHost && (
                                    <button
                                        type="button"
                                        className="btn-primary btn-lg flex items-center justify-center gap-2"
                                        onClick={async () => {
                                            const requestBody: QuizRoomStartRequestBody = {
                                                quizRoomId: quizRoomState.currentRoom.id,
                                            };

                                            await dispatch(startQuizRoom(requestBody) as any);
                                        }}
                                    >
                                        <span>Start Game</span>
                                        <PlayIcon className="h-4 w-4" />
                                    </button>
                                )}

                                <button
                                    type="button"
                                    className="btn-secondary-light btn-lg flex items-center justify-center gap-2"
                                    onClick={async () => {
                                        const attributes: QuizRoomDeleteParticipantAttributes = {
                                            quizRoomId: quizRoomState.currentRoom.id,
                                            participantId: userState.userId,
                                        };

                                        await dispatch(deleteParticipant(attributes) as any);
                                        if (isHost) {
                                            const params: QuizRoomDeleteParams = {
                                                quizRoomId: quizRoomState.currentRoom.id,
                                            };
                                            await dispatch(deleteQuizRoom(params) as any);
                                        }
                                        dispatch(resetRooms() as any);
                                        dispatch(resetCurrentRoom() as any);
                                    }}
                                >
                                    <span>{isHost ? 'Delete Room' : 'Leave Room'}</span>
                                    <ArrowRightIcon className="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
