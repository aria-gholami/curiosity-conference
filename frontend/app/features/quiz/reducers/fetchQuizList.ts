import { createAsyncThunk } from '@reduxjs/toolkit';
import type { RootState } from '~/redux/store';
import type { QuizState } from '~/features/quiz/state/Initial';
import type { QuizListResponse } from '~/features/quiz/state/QuizListResponse';

export const fetchQuizList = createAsyncThunk<QuizState, void, { state: RootState }>(
    'quizList/fetch',
    async () => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/quiz`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
            });
            if (!response.ok) {
                throw new Error('Response by fetch quiz list api is invalid.');
            }

            const responseBody: QuizListResponse = await response.json();
            const state: QuizState = {
                quizzes: responseBody.data.map((quiz) => ({
                    id: quiz.id,
                    title: quiz.title,
                    description: quiz.description,
                    category: quiz.category,
                })),
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
            const { quiz } = getState() as RootState;
            if (quiz.isLoading || quiz.quizzes.length !== 0) {
                return false;
            }
        },
    },
);
