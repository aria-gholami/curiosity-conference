import { createAsyncThunk } from '@reduxjs/toolkit';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import type { RootState } from '~/redux/store';
import type { HighscoreByAllQuizRoomResponse } from '~/features/quizRoom/state/HighscoreByAllQuizRoomResponse';

export const fetchHighscoreByAllQuizRoom = createAsyncThunk<
    QuizRoomState,
    void,
    { state: RootState }
>(
    'quizRoom/fetchAllHighscores',
    async (_arg, { getState }) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/quiz/room/highscores`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
            });
            if (!response.ok) {
                throw new Error('Response by fetch highscores by All quiz rooms api is invalid.');
            }

            const responseBody: HighscoreByAllQuizRoomResponse = await response.json();
            const { quizRoom } = getState() as RootState;

            const currentRoomHighscore = responseBody.data.find(
                (item) => item.quizRoomTitle === quizRoom.currentRoom.title,
            );

            const state: QuizRoomState = {
                ...quizRoom,
                currentRoom: {
                    ...quizRoom.currentRoom,
                    score: currentRoomHighscore
                        ? currentRoomHighscore.quizRoomScore
                        : (quizRoom.currentRoom.score ?? 0),
                },
                rooms: quizRoom.rooms
                    .map((room) => {
                        const highscore = responseBody.data.find(
                            (item) => item.quizRoomTitle === room.title,
                        );
                        return {
                            ...room,
                            score: highscore ? highscore.quizRoomScore : (room.score ?? 0),
                        };
                    })
                    .sort((a, b) => (b.score ?? 0) - (a.score ?? 0)),
                isLoading: false,
            };

            return state;
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
