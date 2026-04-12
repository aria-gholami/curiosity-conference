import { createAsyncThunk } from '@reduxjs/toolkit';
import type { RootState } from '~/redux/store';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import type QuizRoomJoinRequestBody from '~/features/quizRoom/state/QuizRoomJoinRequestBody';
import type { QuizRoomJoinResponse } from '~/features/quizRoom/state/QuizRoomJoinResponse';

export const joinQuizRoom = createAsyncThunk<
    QuizRoomState,
    QuizRoomJoinRequestBody,
    { state: RootState }
>(
    'quizRoom/join',
    async (requestBody: QuizRoomJoinRequestBody) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/quiz/room/join`;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
                body: JSON.stringify(requestBody),
            });
            if (!response.ok) {
                throw new Error('Response by join quiz api is invalid.');
            }

            const responseBody: QuizRoomJoinResponse = await response.json();
            const state: QuizRoomState = {
                currentRoom: {
                    id: responseBody.id,
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
