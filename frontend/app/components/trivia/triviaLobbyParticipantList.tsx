import { XMarkIcon } from '@heroicons/react/24/solid';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useAppSelector } from '~/redux/hooks';
import type { UserState } from '~/features/user/state/Initial';
import type TriviaLobbyParticipantListProps from '~/components/trivia/types/TriviaLobbyParticipantListProps';

export default function TriviaLobbyParticipantList(props: TriviaLobbyParticipantListProps) {
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);
    const isHost = userState.userId === quizRoomState.currentRoom.host.id;

    return (
        <div className="trivia-lobby-participant-list">
            {quizRoomState.currentRoom.participants.map((participant) => (
                <div key={participant.userId} className="trivia-lobby-participant-list-item">
                    <div className="trivia-lobby-participant-list-info">
                        <div className="trivia-lobby-participant-list-avatar">
                            {participant.userName.slice(0, 1).toUpperCase()}
                        </div>

                        <div className="trivia-lobby-participant-list-details">
                            <p
                                className="trivia-lobby-participant-list-name"
                                title={participant.userName}
                            >
                                {participant.userName}
                            </p>

                            <p className="trivia-lobby-participant-list-role">
                                {participant.userId === quizRoomState.currentRoom.host.id
                                    ? 'Host'
                                    : 'Player'}
                            </p>
                        </div>
                    </div>

                    {participant.userId === quizRoomState.currentRoom.host.id && (
                        <span className="trivia-lobby-participant-list-host-badge">Host</span>
                    )}

                    {isHost && participant.userId !== quizRoomState.currentRoom.host.id && (
                        <button
                            type="button"
                            title="Remove participant"
                            aria-label={`Remove ${participant.userName}`}
                            className="trivia-lobby-participant-list-remove-button"
                            onClick={() => {
                                props.setConfirmation({
                                    type: 'remove',
                                    participantId: participant.userId,
                                    participantName: participant.userName,
                                });
                            }}
                        >
                            <XMarkIcon
                                className="trivia-lobby-participant-list-remove-icon"
                                aria-hidden="true"
                            />
                        </button>
                    )}
                </div>
            ))}
        </div>
    );
}
