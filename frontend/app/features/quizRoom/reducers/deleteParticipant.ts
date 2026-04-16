import { createAsyncThunk } from '@reduxjs/toolkit';
import type { RootState } from '~/redux/store';
import type QuizRoomDeleteParticipantAttributes from '~/features/quizRoom/state/QuizRoomDeleteParticipantAttributes';

export const deleteParticipant = createAsyncThunk<
    QuizRoomDeleteParticipantAttributes,
    QuizRoomDeleteParticipantAttributes,
    { state: RootState }
>(
    'quizRoom/leave',
    async (attributes: QuizRoomDeleteParticipantAttributes) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/quiz/room/${attributes.quizRoomId}/participant/${attributes.participantId}`;
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
            });
            if (!response.ok) {
                throw new Error('Response by leave quiz room api is invalid.');
            }

            return attributes;
        } catch (error) {
            console.error(error);

            throw error;
        }
    },
    {
        condition: (_arg, { getState }) => {
            const { quizRoom } = getState() as RootState;
            if (quizRoom.isLoading) {
                return false;
            }
        },
    },
);
