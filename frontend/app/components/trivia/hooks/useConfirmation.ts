import { useState } from 'react';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { deleteParticipant } from '~/features/quizRoom/reducers/deleteParticipant';
import { deleteQuizRoom } from '~/features/quizRoom/reducers/deleteQuizRoom';
import { resetCurrentRoom, resetRooms } from '~/features/quizRoom/quizRoomSlice';
import type QuizRoomDeleteParticipantAttributes from '~/features/quizRoom/state/QuizRoomDeleteParticipantAttributes';
import type QuizRoomDeleteParams from '~/features/quizRoom/state/QuizRoomDeleteRequestBody';
import type QuizRoomStartRequestBody from '~/features/quizRoom/state/QuizRoomStartRequestBody';
import { startQuizRoom } from '~/features/quizRoom/reducers/startQuizRoom';
import { revealAnswer } from '~/features/quizRoom/reducers/revealAnswer';

type ConfirmationAction =
    | {
          type: 'remove';
          participantId: string;
          participantName: string;
      }
    | { type: 'delete' }
    | { type: 'leave' }
    | { type: 'start' }
    | { type: 'next' };

export const useConfirmation = () => {
    const dispatch = useAppDispatch();
    const roomId = useAppSelector((state) => state.quizRoom.currentRoom.id);
    const userId = useAppSelector((state) => state.user.userId);
    const [confirmation, setConfirmation] = useState<ConfirmationAction | null>(null);
    const [isProcessing, setIsProcessing] = useState(false);

    const handleConfirm = async () => {
        if (!confirmation || isProcessing) {
            return;
        }

        setIsProcessing(true);
        try {
            switch (confirmation.type) {
                case 'remove': {
                    const attributes: QuizRoomDeleteParticipantAttributes = {
                        quizRoomId: roomId,
                        participantId: confirmation.participantId,
                    };
                    await dispatch(deleteParticipant(attributes) as any).unwrap();
                    break;
                }
                case 'leave': {
                    const attributes: QuizRoomDeleteParticipantAttributes = {
                        quizRoomId: roomId,
                        participantId: userId,
                    };
                    await dispatch(deleteParticipant(attributes) as any).unwrap();
                    dispatch(resetRooms());
                    dispatch(resetCurrentRoom());
                    break;
                }
                case 'delete': {
                    const params: QuizRoomDeleteParams = {
                        quizRoomId: roomId,
                    };

                    await dispatch(deleteQuizRoom(params) as any).unwrap();
                    dispatch(resetRooms());
                    dispatch(resetCurrentRoom());
                    break;
                }
                case 'start': {
                    const requestBody: QuizRoomStartRequestBody = {
                        quizRoomId: roomId,
                    };

                    await dispatch(startQuizRoom(requestBody) as any).unwrap();

                    break;
                }
                case 'next': {
                    await dispatch(revealAnswer(roomId) as any).unwrap();
                    break;
                }
            }

            setConfirmation(null);
        } catch (error) {
            console.error('Confirmation action failed:', error);
        } finally {
            setIsProcessing(false);
        }
    };

    const confirmationTitle =
        confirmation?.type === 'remove'
            ? 'Remove participant?'
            : confirmation?.type === 'delete'
              ? 'Delete room?'
              : confirmation?.type === 'start'
                ? 'Start game?'
                : confirmation?.type === 'next'
                  ? 'Next question?'
                  : 'Leave room?';
    const confirmationMessage =
        confirmation?.type === 'remove'
            ? `Are you sure you want to remove ${confirmation.participantName} from the room?`
            : confirmation?.type === 'delete'
              ? 'Are you sure you want to delete this room? All participants will be removed.'
              : confirmation?.type === 'start'
                ? 'Are you sure you want to start the game? All participants will begin the quiz, and no new players will be able to join once the game has started.'
                : confirmation?.type === 'next'
                  ? "Are you sure you want to proceed? The correct answer will be revealed to all players. This action cannot be undone, and you won't be able to return to the previous question."
                  : 'Are you sure you want to leave this room?';
    const confirmationLabel =
        confirmation?.type === 'remove'
            ? 'Remove'
            : confirmation?.type === 'delete'
              ? 'Delete Room'
              : confirmation?.type === 'start'
                ? 'Start Game'
                : confirmation?.type === 'next'
                  ? 'Next Question'
                  : 'Leave Room';

    return {
        confirmation,
        setConfirmation,
        isProcessing,
        handleConfirm,
        confirmationTitle,
        confirmationMessage,
        confirmationLabel,
    };
};
