import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, { type QuizState } from './state/Initial';
import { fetchQuizQuestion } from '~/features/quiz/reducers/fetchQuizQuestion';
import { continueOrFinishQuiz } from '~/features/quizRoom/reducers/continueOrFinishQuiz';

const quizSlice = createSlice({
    name: 'quiz',
    initialState,
    reducers: {
        reset(state: QuizState): void {
            state.quiz = {
                roomId: '',
                question: undefined,
            };
            state.isLoading = false;
        },
        removeCurrentQuestion(state: QuizState): void {
            state.quiz.question = undefined;
        },
    },
    extraReducers: (builder) => {
        builder
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
            .addCase(continueOrFinishQuiz.fulfilled, (state: QuizState) => {
                state.quiz.question = undefined;
            });
    },
});

export const { reset, removeCurrentQuestion } = quizSlice.actions;

export default quizSlice;
