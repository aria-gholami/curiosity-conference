import { createAsyncThunk } from '@reduxjs/toolkit';
import type { RootState } from '~/redux/store';
import type { QuizState } from '~/features/quiz/state/Initial';
import type { QuizRoomQuestionListResponse } from '~/features/quiz/state/QuizQuestionListResponse';

export const fetchQuizQuestion = createAsyncThunk<QuizState, string, { state: RootState }>(
    'quizRoomQuestion/fetch',
    async (quizRoomId: string, { getState }) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/trivia/quiz/room/${quizRoomId}/question`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
            });
            if (!response.ok) {
                throw new Error('Response by fetch quiz room question api is invalid.');
            }

            const responseBody: QuizRoomQuestionListResponse = await response.json();
            const { quiz } = getState();
            const state: QuizState = {
                quiz: {
                    id: quizRoomId,
                    title: quiz.quiz.title ?? '',
                    description: quiz.quiz?.description ?? '',
                    category: quiz.quiz?.category ?? '',
                    question: {
                        title: responseBody.question,
                        answers: responseBody.answers,
                    },
                },
                isLoading: false,
            };

            return state;
        } catch (error) {
            console.error(error);

            throw error;
        }
    },
    {
        condition: (_, { getState }) => {
            const { quiz } = getState() as RootState;
            if (quiz.quiz && quiz.quiz.question) {
                return false;
            }
        },
    },
);
