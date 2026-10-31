import { UserGroupIcon, XMarkIcon } from '@heroicons/react/24/solid';
import { useAppSelector } from '~/redux/hooks';
import { useBodyScrollLock } from '~/hooks/useBodyScrollLock';
import type TriviaRoomParticipantsPopupProps from '~/components/trivia/types/TriviaRoomParticipantsPopupProps';

export default function TriviaRoomParticipantsPopup({
    room,
    open,
    onClose,
}: TriviaRoomParticipantsPopupProps) {
    const currentRoom = useAppSelector((state) =>
        state.quizRoom.rooms.find((item) => item.id === room.id),
    );
    const displayRoom = currentRoom ?? room;
    useBodyScrollLock(open);
    if (!open) {
        return null;
    }

    return (
        <div
            className="popup"
            role="dialog"
            aria-modal="true"
            aria-labelledby="trivia-room-participants-title"
        >
            <div className="popup-overlay" onClick={onClose} />
            <div className="glass popup-container">
                <div className="popup-glow-turquoise" />
                <div className="popup-glow-dark-blue" />
                <div className="popup-gradient" />

                <div className="popup-content">
                    <div className="trivia-room-participants-popup-header">
                        <div className="trivia-room-participants-popup-heading">
                            <div className="popup-badge">
                                <UserGroupIcon className="trivia-room-participants-popup-badge-icon" />
                                <span>Participants</span>
                            </div>
                            <h2 id="trivia-room-participants-title" className="popup-title">
                                {displayRoom.title}
                            </h2>
                            <p className="trivia-room-participants-popup-description">
                                All participants in this room ({displayRoom.participants.length})
                            </p>
                        </div>

                        <button
                            type="button"
                            onClick={onClose}
                            className="trivia-room-participants-popup-close"
                            aria-label="Close participants"
                        >
                            <XMarkIcon className="trivia-room-participants-popup-close-icon" />
                        </button>
                    </div>

                    <div className="trivia-room-participants-popup-list">
                        {displayRoom.participants.length === 0 ? (
                            <div className="trivia-room-participants-popup-empty">
                                No participants in this room yet.
                            </div>
                        ) : (
                            displayRoom.participants.map((participant) => {
                                const isHost = participant.userId === displayRoom.host.id;
                                return (
                                    <div
                                        key={participant.userId}
                                        className="trivia-room-participants-popup-item"
                                    >
                                        <div className="trivia-room-participants-popup-avatar">
                                            {participant.userName.slice(0, 1).toUpperCase()}
                                        </div>
                                        <div className="trivia-room-participants-popup-participant-info">
                                            <p
                                                className="trivia-room-participants-popup-name"
                                                title={participant.userName}
                                            >
                                                {participant.userName}
                                            </p>
                                            <p className="trivia-room-participants-popup-role">
                                                {isHost ? 'Host' : 'Player'}
                                            </p>
                                        </div>
                                    </div>
                                );
                            })
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
