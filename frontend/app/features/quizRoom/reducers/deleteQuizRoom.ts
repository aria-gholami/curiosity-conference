import { createAsyncThunk } from '@reduxjs/toolkit';
import type { RootState } from '~/redux/store';
import type QuizRoomDeleteParams from '~/features/quizRoom/state/QuizRoomDeleteRequestBody';

export const deleteQuizRoom = createAsyncThunk<void, QuizRoomDeleteParams, { state: RootState }>(
    'quizRoom/delete',
    async (params: QuizRoomDeleteParams) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/trivia/quiz/room?quizRoomId=${params.quizRoomId}`;
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
            });
            if (!response.ok) {
                throw new Error('Response by delete quiz room api is invalid.');
            }
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
