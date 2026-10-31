import { setSelectedAnswerId } from '~/features/quizRoom/quizRoomSlice';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import type { UserState } from '~/features/user/state/Initial';
import type TriviaQuizQuestionProps from '~/components/trivia/types/TriviaQuizQuestionProps';

export default function TriviaQuizQuestion(props: TriviaQuizQuestionProps) {
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);

    const selectedAnswerId = quizRoomState.currentRoom.selectedAnswerId;
    const isHost = userState.userId === quizRoomState.currentRoom.host.id;

    return (
        <div className="glass trivia-quiz-question">
            <div className="card trivia-quiz-question-card">
                <h2 className="trivia-quiz-question-title">{props.currentQuestion.question}</h2>
            </div>

            <div className="trivia-quiz-question-answers">
                {props.currentQuestion.answers.map((answer, index) => {
                    const isSelected = selectedAnswerId === answer.id;
                    const isCorrectRevealed = quizRoomState.isRevealing && answer.isCorrect;
                    const isIncorrectRevealed = quizRoomState.isRevealing && !answer.isCorrect;

                    return (
                        <button
                            key={answer.id}
                            type="button"
                            onClick={() => {
                                if (!quizRoomState.isRevealing && isHost) {
                                    dispatch(setSelectedAnswerId(answer.id));
                                }
                            }}
                            disabled={!isHost || quizRoomState.isRevealing}
                            className={[
                                'trivia-quiz-question-answer',
                                isCorrectRevealed
                                    ? 'reveal-correct trivia-quiz-question-answer-correct'
                                    : isSelected
                                      ? 'trivia-quiz-question-answer-selected'
                                      : 'trivia-quiz-question-answer-default',
                                isIncorrectRevealed ? 'trivia-quiz-question-answer-dimmed' : '',
                            ]
                                .filter(Boolean)
                                .join(' ')}
                        >
                            <div
                                className={`trivia-quiz-question-answer-letter ${
                                    isSelected
                                        ? 'trivia-quiz-question-answer-letter-selected'
                                        : 'trivia-quiz-question-answer-letter-default'
                                }`}
                            >
                                {String.fromCharCode(65 + index)}
                            </div>

                            <span className="trivia-quiz-question-answer-text">{answer.title}</span>
                        </button>
                    );
                })}
            </div>

            {isHost ? (
                <div className="trivia-quiz-question-host-actions">
                    <button
                        type="button"
                        disabled={quizRoomState.isRevealing}
                        onClick={() => {
                            props.setConfirmation({ type: 'next' });
                        }}
                        className={`btn-primary trivia-quiz-question-next-button ${
                            quizRoomState.isRevealing
                                ? 'trivia-quiz-question-next-button-disabled'
                                : ''
                        }`}
                    >
                        Next Question
                    </button>

                    <button
                        type="button"
                        onClick={() => {
                            props.setConfirmation({ type: 'delete' });
                        }}
                        className="trivia-quiz-question-delete-button"
                    >
                        Delete Room
                    </button>
                </div>
            ) : (
                <div className="trivia-quiz-question-player-actions">
                    <button
                        type="button"
                        onClick={() => {
                            props.setConfirmation({ type: 'leave' });
                        }}
                        className="trivia-quiz-question-leave-button"
                    >
                        Leave Room
                    </button>
                </div>
            )}
        </div>
    );
}
