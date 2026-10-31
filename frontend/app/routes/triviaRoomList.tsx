import { PlusIcon } from '@heroicons/react/24/solid';
import { useState } from 'react';
import { useFetchQuizRoomList } from '~/routes/hooks/useFetchQuizRoomList';
import { useDisplayRegistrationPopup } from '~/routes/hooks/useDisplayRegistrationPopup';
import type { QuizRoom, QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useAppSelector } from '~/redux/hooks';
import RegistrationPopup from '~/components/registration/registrationPopup';
import TriviaRoomCreatePopup from '~/components/trivia/triviaRoomCreatePopup';
import { useFetchActiveQuizRoom } from '~/routes/hooks/useFetchActiveQuizRoom';
import { useFetchHighscoreByAllQuizRoom } from '~/routes/hooks/useFetchHighscoreByAllQuizRoom';
import TriviaRoomListItem from '~/components/trivia/triviaRoomListItem';
import TriviaRoomParticipantsPopup from '~/components/trivia/triviaRoomParticipantsPopup';
import { useResetQuiz } from '~/routes/hooks/useResetQuiz';

export default function TriviaRoomList() {
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);

    const [isRegistrationPopupOpen, setIsRegistrationPopupOpen] = useState(true);
    const [isCreateRoomPopupOpen, setIsCreateRoomPopupOpen] = useState(false);
    const [selectedParticipantsRoom, setSelectedParticipantsRoom] = useState<QuizRoom | null>(null);

    useDisplayRegistrationPopup(setIsRegistrationPopupOpen);
    useFetchActiveQuizRoom();
    useFetchQuizRoomList();
    useFetchHighscoreByAllQuizRoom();
    useResetQuiz();

    return (
        <div className="page-background">
            <div className="page-background-overlay" />
            <div className="page-glow-turquoise" />
            <div className="page-glow-dark-blue" />
            <div className="page-glow-light-blue" />

            <RegistrationPopup open={isRegistrationPopupOpen} />
            <TriviaRoomCreatePopup
                open={isCreateRoomPopupOpen}
                onClose={() => setIsCreateRoomPopupOpen(false)}
            />

            <div className="trivia-room-list-container">
                <div className="trivia-room-list-header">
                    <div>
                        <p className="trivia-room-list-subtitle">Trivia Challenge</p>

                        <h1 className="trivia-room-list-title">Quiz Rooms</h1>

                        <p className="trivia-room-list-description">
                            Join an existing room or create a new quiz room for other conference
                            participants.
                        </p>
                    </div>

                    <button
                        type="button"
                        onClick={() => setIsCreateRoomPopupOpen(true)}
                        className="btn btn-primary btn-lg trivia-room-list-create-button"
                    >
                        <PlusIcon className="trivia-room-list-create-icon" />
                        <span>Create New Room</span>
                    </button>
                </div>

                <div className="glass trivia-room-list-room-list">
                    <div className="trivia-room-list-room-items">
                        {quizRoomState.rooms.map((room: QuizRoom) => (
                            <TriviaRoomListItem
                                key={room.id}
                                room={room}
                                onShowParticipants={setSelectedParticipantsRoom}
                            />
                        ))}
                    </div>
                </div>
                {selectedParticipantsRoom && (
                    <TriviaRoomParticipantsPopup
                        room={selectedParticipantsRoom}
                        open={true}
                        onClose={() => setSelectedParticipantsRoom(null)}
                    />
                )}
            </div>
        </div>
    );
}
