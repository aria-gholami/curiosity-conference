import { createAsyncThunk } from '@reduxjs/toolkit';
import type { RootState } from '~/redux/store';
import type QuizRoomStartRequestBody from '~/features/quizRoom/state/QuizRoomStartRequestBody';

export const startQuizRoom = createAsyncThunk<void, QuizRoomStartRequestBody, { state: RootState }>(
    'quizRoom/start',
    async (requestBody: QuizRoomStartRequestBody) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/quiz/room/start`;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
                body: JSON.stringify(requestBody),
            });
            if (!response.ok) {
                throw new Error('Response by start quiz room api is invalid.');
            }
        } catch (error) {
            console.error(error);

            throw error;
        }
    },
    {
        condition: (_arg, { getState }) => {
            const { quizRoom } = getState() as RootState;
            if (quizRoom.isLoading || quizRoom.currentRoom.status === 'started') {
                return false;
            }
        },
    },
);
