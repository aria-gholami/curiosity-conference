import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, { type QuizState } from './state/Initial';
import { fetchQuizList } from '~/features/quiz/reducers/fetchQuizList';
import { fetchQuizQuestion } from '~/features/quiz/reducers/fetchQuizQuestion';
import { continueOrFinishQuiz } from '~/features/quizRoom/reducers/continueOrFinishQuiz';

const quizSlice = createSlice({
    name: 'quiz',
    initialState,
    reducers: {
        reset(state: QuizState): void {
            state.quiz = {
                id: '',
                title: '',
                description: '',
                category: '',
                question: undefined,
            };
            state.isLoading = false;
        },
        removeCurrentQuestion(state: QuizState, action): void {
            state.quiz.question = undefined;
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(fetchQuizList.pending, (state: QuizState) => {
                state.isLoading = true;
            })
            .addCase(
                fetchQuizList.fulfilled,
                (state: QuizState, action: PayloadAction<QuizState>) => {
                    state.quiz = action.payload.quiz;
                    state.isLoading = false;
                },
            )
            .addCase(fetchQuizList.rejected, (state: QuizState) => {
                state.quiz = {
                    id: '',
                    title: '',
                    description: '',
                    category: '',
                    question: undefined,
                };
                state.isLoading = false;
            })
            .addCase(fetchQuizQuestion.pending, (state: QuizState) => {
                state.isLoading = true;
            })
            .addCase(
                fetchQuizQuestion.fulfilled,
                (state: QuizState, action: PayloadAction<QuizState>) => {
                    state.quiz = action.payload.quiz;
                    state.isLoading = false;
                },
            )
            .addCase(fetchQuizQuestion.rejected, (state: QuizState) => {
                state.isLoading = false;
            })
            .addCase(continueOrFinishQuiz.fulfilled, (state: QuizState, action) => {
                state.quiz.question = undefined;
            });
    },
});

export const { reset, removeCurrentQuestion } = quizSlice.actions;

export default quizSlice;
