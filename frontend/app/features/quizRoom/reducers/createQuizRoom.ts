import { createAsyncThunk } from '@reduxjs/toolkit';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import type { QuizRoomCreateRequestBody } from '~/features/quizRoom/state/QuizRoomCreateRequestBody';
import type { RootState } from '~/redux/store';
import type { QuizRoomCreateResponse } from '~/features/quizRoom/state/QuizRoomCreateResponse';
import type { Participant } from '~/features/quizRoom/state/QuizRoomListResponse';

export const createQuizRoom = createAsyncThunk<
    QuizRoomState,
    QuizRoomCreateRequestBody,
    { state: RootState }
>(
    'quizRoom/create',
    async (requestBody: QuizRoomCreateRequestBody) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/quiz/room`;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
                body: JSON.stringify(requestBody),
            });
            if (!response.ok) {
                throw new Error('Response by create quiz room api is invalid.');
            }

            const responseBody: QuizRoomCreateResponse = await response.json();
            const state: QuizRoomState = {
                currentRoom: {
                    id: responseBody.id,
                    title: requestBody.title,
                    quiz: {
                        title: responseBody.quiz.title,
                    },
                    host: {
                        id: responseBody.host.id,
                        userName: responseBody.host.userName,
                    },
                    status: responseBody.status,
                    participants: responseBody.participants.map((participant: Participant) => {
                        return {
                            userName: participant.userName,
                            userId: participant.userId,
                            score: 0,
                        };
                    }),
                },
                rooms: [],
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
