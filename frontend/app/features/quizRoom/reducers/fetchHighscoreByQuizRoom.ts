import { createAsyncThunk } from '@reduxjs/toolkit';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import type { RootState } from '~/redux/store';
import type { HighscoreByQuizRoomResponse } from '~/features/quizRoom/state/HighscoreByQuizRoomResponse';

export const fetchHighscoreByQuizRoom = createAsyncThunk<
    QuizRoomState,
    string,
    { state: RootState }
>(
    'quizRoom/fetchHighscores',
    async (quizRoomId: string, { getState }) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/quiz/room/${quizRoomId}/highscores`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
            });
            if (!response.ok) {
                throw new Error('Response by fetch highscore by quiz room api is invalid.');
            }

            const responseBody: HighscoreByQuizRoomResponse = await response.json();
            const { quizRoom } = getState() as RootState;

            const state: QuizRoomState = {
                ...quizRoom,
                currentRoom:
                    quizRoom.currentRoom.id === quizRoomId
                        ? {
                              ...quizRoom.currentRoom,
                              participants: quizRoom.currentRoom.participants.map((participant) => {
                                  const highscore = responseBody.data.find(
                                      (item) => item.userName === participant.userName,
                                  );
                                  return {
                                      ...participant,
                                      score: highscore ? highscore.score : (participant.score ?? 0),
                                  };
                              }),
                          }
                        : quizRoom.currentRoom,
                rooms: quizRoom.rooms.map((room) => {
                    if (room.id === quizRoomId) {
                        return {
                            ...room,
                            participants: room.participants.map((participant) => {
                                const highscore = responseBody.data.find(
                                    (item) => item.userName === participant.userName,
                                );
                                return {
                                    ...participant,
                                    score: highscore ? highscore.score : (participant.score ?? 0),
                                };
                            }),
                        };
                    }
                    return room;
                }),
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
