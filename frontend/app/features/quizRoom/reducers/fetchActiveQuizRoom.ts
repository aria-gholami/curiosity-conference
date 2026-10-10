import { createAsyncThunk } from '@reduxjs/toolkit';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import type { RootState } from '~/redux/store';
import type { ActiveQuizRoomLoadResponse } from '~/features/quizRoom/state/ActiveQuizRoomLoadResponse';
import type { ActiveQuizRoomLoadRequestBody } from '~/features/quizRoom/state/ActiveQuizRoomLoadRequestBody';

export const fetchActiveQuizRoom = createAsyncThunk<
    QuizRoomState,
    ActiveQuizRoomLoadRequestBody,
    { state: RootState }
>(
    'quizRoomActive/fetch',
    async (requestBody: ActiveQuizRoomLoadRequestBody) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/trivia/quiz/room/user/${requestBody.userId}`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
            });
            if (!response.ok) {
                throw new Error('Response by fetch active quiz room api is invalid.');
            }
            if (response.status === 204) {
                return {
                    currentRoom: {
                        id: '',
                        title: '',
                        quiz: {
                            title: '',
                        },
                        host: {
                            id: '',
                            userName: '',
                        },
                        status: '',
                        participants: [],
                    },
                    rooms: [],
                    isLoading: true,
                };
            }

            const responseBody: ActiveQuizRoomLoadResponse = await response.json();
            const state: QuizRoomState = {
                currentRoom: {
                    id: responseBody.id,
                    title: responseBody.title,
                    quiz: {
                        title: responseBody.quiz.title,
                    },
                    host: {
                        id: responseBody.host.id,
                        userName: responseBody.host.userName,
                    },
                    status: responseBody.status,
                    participants: responseBody.participants,
                },
                rooms: [],
                isLoading: true,
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
            if (quizRoom.isLoading || quizRoom.currentRoom.id !== '') {
                return false;
            }
        },
    },
);
