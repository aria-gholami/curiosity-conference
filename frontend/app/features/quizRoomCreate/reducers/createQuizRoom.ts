import { createAsyncThunk } from '@reduxjs/toolkit';
import type { QuizRoomCreateState } from '~/features/quizRoomCreate/state/Initial';
import type { QuizRoomCreateRequestBody } from '~/features/quizRoomCreate/state/QuizRoomCreateRequestBody';
import type { RootState } from '~/redux/store';
import type { QuizRoomCreateResponse } from '~/features/quizRoomCreate/state/QuizRoomCreateResponse';

export const createQuizRoom = createAsyncThunk<
    QuizRoomCreateState,
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
                    Authorization: `Bearer ${requestBody.authenticationToken}`,
                },
                body: JSON.stringify(requestBody),
            });
            if (!response.ok) {
                throw new Error('Response by create quiz api is invalid.');
            }

            const responseBody: QuizRoomCreateResponse = await response.json();
            const state: QuizRoomCreateState = {
                id: responseBody.id,
                quizId: requestBody.quizId,
                hostId: requestBody.hostId,
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
            const { quizRoomCreate } = getState() as RootState;
            if (quizRoomCreate.isLoading || quizRoomCreate.id !== '') {
                return false;
            }
        },
    },
);
