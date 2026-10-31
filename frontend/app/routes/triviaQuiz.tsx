import RegistrationPopup from '~/components/registration/registrationPopup';
import { useState } from 'react';
import { useDisplayRegistrationPopup } from '~/routes/hooks/useDisplayRegistrationPopup';
import { useFetchActiveQuizRoom } from '~/routes/hooks/useFetchActiveQuizRoom';
import { useLobbyNotFoundRedirect } from '~/routes/hooks/useLobbyNotFoundRedirect';
import { useNotStartedQuiz } from '~/routes/hooks/useNotStartedQuiz';
import { useFetchQuizRoomQuestion } from '~/routes/hooks/useFetchQuizRoomQuestion';
import { useAppSelector } from '~/redux/hooks';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useConfirmation } from '~/components/trivia/hooks/useConfirmation';
import ConfirmationPopup from '~/components/trivia/confirmationPopup';
import TriviaQuizQuestion from '~/components/trivia/triviaQuizQuestion';

export default function TriviaQuiz() {
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);

    const [isRegistrationPopupOpen, setIsRegistrationPopupOpen] = useState(true);
    const [currentQuestion, setCurrentQuestion] = useState({
        question: 'Loading...',
        answers: [
            { id: '1', title: 'Loading', isCorrect: true },
            { id: '2', title: 'Loading', isCorrect: false },
            { id: '3', title: 'Loading', isCorrect: false },
            { id: '4', title: 'Loading', isCorrect: false },
        ],
    });

    useDisplayRegistrationPopup(setIsRegistrationPopupOpen);
    useFetchActiveQuizRoom();
    useLobbyNotFoundRedirect();
    useNotStartedQuiz();
    useFetchQuizRoomQuestion(setCurrentQuestion);

    const {
        confirmation,
        setConfirmation,
        isProcessing,
        handleConfirm,
        confirmationTitle,
        confirmationMessage,
        confirmationLabel,
    } = useConfirmation();

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

            <div className="trivia-quiz-container">
                <div className="trivia-quiz-header">
                    <div>
                        <p className="trivia-quiz-subtitle">Trivia Challenge</p>
                        <h1 className="trivia-quiz-title">Quiz Room</h1>
                    </div>
                    <div className="trivia-quiz-room-badge">
                        <span className="trivia-quiz-room-name">
                            Room: {quizRoomState.currentRoom.title}
                        </span>
                    </div>
                </div>

                <div className="trivia-quiz-content">
                    <TriviaQuizQuestion
                        currentQuestion={currentQuestion}
                        setConfirmation={setConfirmation}
                    />
                    <div className="trivia-quiz-participants">
                        {quizRoomState.currentRoom.participants.map((participant) => (
                            <div key={participant.userId} className="trivia-quiz-participant">
                                <div className="trivia-quiz-participant-avatar-container">
                                    <div className="trivia-quiz-participant-avatar">
                                        {participant.userName.slice(0, 1).toUpperCase()}
                                    </div>
                                    <div className="trivia-quiz-participant-status" />
                                </div>

                                <span
                                    className="trivia-quiz-participant-name"
                                    title={participant.userName}
                                >
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
