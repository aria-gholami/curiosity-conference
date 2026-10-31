import { ArrowRightIcon, PlayIcon } from '@heroicons/react/24/solid';
import { useState } from 'react';
import { useDisplayRegistrationPopup } from '~/routes/hooks/useDisplayRegistrationPopup';
import RegistrationPopup from '~/components/registration/registrationPopup';
import ConfirmationPopup from '~/components/trivia/confirmationPopup';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import type { UserState } from '~/features/user/state/Initial';
import { useAppSelector } from '~/redux/hooks';
import { useFetchActiveQuizRoom } from '~/routes/hooks/useFetchActiveQuizRoom';
import { useLobbyNotFoundRedirect } from '~/routes/hooks/useLobbyNotFoundRedirect';
import { useStartedQuiz } from '~/routes/hooks/useStartedQuiz';
import { useConfirmation } from '~/components/trivia/hooks/useConfirmation';
import TriviaLobbyParticipantList from '~/components/trivia/triviaLobbyParticipantList';

export default function TriviaLobby() {
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);
    const [isRegistrationPopupOpen, setIsRegistrationPopupOpen] = useState(true);

    useDisplayRegistrationPopup(setIsRegistrationPopupOpen);
    useFetchActiveQuizRoom();
    useLobbyNotFoundRedirect();
    useStartedQuiz();
    const {
        confirmation,
        setConfirmation,
        isProcessing,
        handleConfirm,
        confirmationTitle,
        confirmationMessage,
        confirmationLabel,
    } = useConfirmation();

    const isHost = userState.userId === quizRoomState.currentRoom.host.id;

    return (
        <div className="page-background">
            <div className="page-background-overlay" />
            <div className="page-glow-turquoise" />
            <div className="page-glow-dark-blue" />
            <div className="page-glow-light-blue" />
            <RegistrationPopup open={isRegistrationPopupOpen} />
            <ConfirmationPopup
                open={confirmation !== null}
                title={confirmationTitle}
                message={confirmationMessage}
                confirmLabel={confirmationLabel}
                onCancel={() => setConfirmation(null)}
                onConfirm={handleConfirm}
                loading={isProcessing}
            />

            <div className="trivia-lobby-container">
                <div className="trivia-lobby-header">
                    <div>
                        <p className="trivia-lobby-subtitle">Trivia Challenge</p>
                        <h1 className="trivia-lobby-title">Lobby Room</h1>
                        <p className="trivia-lobby-description">
                            Wait for other participants to join, share the room code, and start the
                            game when everyone is ready.
                        </p>
                    </div>
                </div>

                <div className="trivia-lobby-content">
                    <div className="glass trivia-lobby-panel">
                        <div className="card trivia-lobby-card">
                            <div className="trivia-lobby-room-header">
                                <div className="trivia-lobby-room-info">
                                    <h2 className="trivia-lobby-room-title">
                                        {quizRoomState.currentRoom.title}
                                    </h2>
                                    <h3 className="trivia-lobby-participants-title">
                                        Participants
                                    </h3>
                                    <p className="trivia-lobby-participants-description">
                                        Everyone currently in the lobby
                                    </p>
                                </div>

                                <div className="trivia-lobby-participants-count">
                                    {quizRoomState.currentRoom.participants.length} joined
                                </div>
                            </div>

                            <TriviaLobbyParticipantList setConfirmation={setConfirmation} />

                            <div className="trivia-lobby-actions">
                                {isHost && (
                                    <button
                                        type="button"
                                        className="btn-primary btn-lg trivia-lobby-start-button"
                                        onClick={() => {
                                            setConfirmation({ type: 'start' });
                                        }}
                                    >
                                        <span>Start Game</span>
                                        <PlayIcon className="trivia-lobby-action-icon" />
                                    </button>
                                )}

                                <button
                                    type="button"
                                    className="btn-secondary-light btn-lg trivia-lobby-leave-button"
                                    onClick={() => {
                                        setConfirmation({
                                            type: isHost ? 'delete' : 'leave',
                                        });
                                    }}
                                >
                                    <span>{isHost ? 'Delete Room' : 'Leave Room'}</span>
                                    <ArrowRightIcon className="trivia-lobby-action-icon" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
