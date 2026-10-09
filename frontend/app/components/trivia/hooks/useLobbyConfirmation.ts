import { useState } from 'react';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { deleteParticipant } from '~/features/quizRoom/reducers/deleteParticipant';
import { deleteQuizRoom } from '~/features/quizRoom/reducers/deleteQuizRoom';
import { resetCurrentRoom, resetRooms } from '~/features/quizRoom/quizRoomSlice';
import type QuizRoomDeleteParticipantAttributes from '~/features/quizRoom/state/QuizRoomDeleteParticipantAttributes';
import type QuizRoomDeleteParams from '~/features/quizRoom/state/QuizRoomDeleteRequestBody';

type ConfirmationAction =
    | {
          type: 'remove';
          participantId: string;
          participantName: string;
      }
    | { type: 'delete' }
    | { type: 'leave' };

export const useLobbyConfirmation = () => {
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
              : 'Leave room?';
    const confirmationMessage =
        confirmation?.type === 'remove'
            ? `Are you sure you want to remove ${confirmation.participantName} from the room?`
            : confirmation?.type === 'delete'
              ? 'Are you sure you want to delete this room? All participants will be removed.'
              : 'Are you sure you want to leave this room?';
    const confirmationLabel =
        confirmation?.type === 'remove'
            ? 'Remove'
            : confirmation?.type === 'delete'
              ? 'Delete Room'
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
