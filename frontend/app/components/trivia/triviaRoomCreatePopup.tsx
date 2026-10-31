import { PlusIcon } from '@heroicons/react/24/solid';
import { useState } from 'react';
import type { FormEvent } from 'react';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { createQuizRoom } from '~/features/quizRoom/reducers/createQuizRoom';
import type { QuizRoomCreateRequestBody } from '~/features/quizRoom/state/QuizRoomCreateRequestBody';
import type { UserState } from '~/features/user/state/Initial';
import { useNavigate } from 'react-router';
import { useBodyScrollLock } from '~/hooks/useBodyScrollLock';
import type TriviaRoomCreatePopupProps from '~/components/trivia/types/TriviaRoomCreatePopupProps';

export default function TriviaRoomCreatePopup({ open, onClose }: TriviaRoomCreatePopupProps) {
    const navigate = useNavigate();
    const dispatch = useAppDispatch();
    const [quizRoomTitle, setQuizRoomTitle] = useState('');
    const [errorMessage, setErrorMessage] = useState('');
    const userState: UserState = useAppSelector((state) => state.user);
    const MAX_ROOM_TITLE_LENGTH = 15;

    useBodyScrollLock(open);
    if (!open) {
        return null;
    }

    const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        if (quizRoomTitle.length === 0) {
            setErrorMessage("The room title can't be empty.");
            return;
        }
        if (quizRoomTitle.length > MAX_ROOM_TITLE_LENGTH) {
            setErrorMessage('The room title must not exceed 15 characters.');
            return;
        }

        const requestBody: QuizRoomCreateRequestBody = {
            hostId: userState.userId,
            title: quizRoomTitle,
        };

        await dispatch(createQuizRoom(requestBody) as any);
        navigate('/trivia/lobby');
    };

    return (
        <div className="popup">
            <div className="popup-overlay" />
            <div className="glass popup-container">
                <div className="popup-glow-turquoise" />
                <div className="popup-glow-dark-blue" />
                <div className="popup-gradient" />

                <form onSubmit={handleSubmit} className="popup-content">
                    <div className="trivia-room-create-popup-header">
                        <div>
                            <div className="popup-badge">Trivia Challenge</div>
                            <h2 className="popup-title">Create Trivia Room</h2>
                            <p className="popup-description">
                                Create a new room for the selected quiz and invite other
                                participants to join.
                            </p>
                        </div>
                    </div>

                    <div className="trivia-room-create-popup-fields">
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

                            <div className="trivia-room-create-popup-input-footer">
                                <div className="trivia-room-create-popup-error-container">
                                    {errorMessage && (
                                        <p className="trivia-room-create-popup-error">
                                            {errorMessage}
                                        </p>
                                    )}
                                </div>
                                <span
                                    className={`trivia-room-create-popup-character-count ${
                                        quizRoomTitle.length > MAX_ROOM_TITLE_LENGTH
                                            ? 'trivia-room-create-popup-character-count-error'
                                            : ''
                                    }`}
                                >
                                    {quizRoomTitle.length}/{MAX_ROOM_TITLE_LENGTH}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div className="popup-actions">
                        <button
                            type="submit"
                            disabled={!quizRoomTitle}
                            className="btn-primary popup-confirm group trivia-room-create-popup-submit"
                        >
                            <span>Create Room</span>
                            <PlusIcon className="trivia-room-create-popup-submit-icon" />
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
