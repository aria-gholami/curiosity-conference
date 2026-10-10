import { createAsyncThunk } from '@reduxjs/toolkit';
import type { RootState } from '~/redux/store';
import type HighscoreUpdatePayload from '~/features/quizRoom/state/HighscoreUpdatePayload';

export const updateHighscore = createAsyncThunk<void, HighscoreUpdatePayload>(
    'quizRoom/updateHighscore',
    async (highscoreUpdatePayload: HighscoreUpdatePayload) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/trivia/highscore`;
            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(highscoreUpdatePayload),
            });
            if (!response.ok) {
                throw new Error('Failed to update Highscore.');
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
