import { createAsyncThunk } from '@reduxjs/toolkit';
import type { RootState } from '~/redux/store';

export const closeQuizRoom = createAsyncThunk<void, string>(
    'quizRoom/close',
    async (quizRoomId: string) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/quiz/room/close`;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ quizRoomId }),
            });
            if (!response.ok) {
                throw new Error('Failed to close quiz room.');
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
