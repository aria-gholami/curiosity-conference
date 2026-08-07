import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, { type QuizState } from './state/Initial';
import { fetchQuizList } from '~/features/quiz/reducers/fetchQuizList';
import { fetchQuizQuestion } from '~/features/quiz/reducers/fetchQuizQuestion';

const quizSlice = createSlice({
    name: 'quizRoom',
    initialState,
    reducers: {
        reset(state: QuizState): void {
            state.quizzes = [];
            state.isLoading = false;
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
                    state.quizzes = action.payload.quizzes;
                    state.isLoading = false;
                },
            )
            .addCase(fetchQuizList.rejected, (state: QuizState) => {
                state.quizzes = [];
                state.isLoading = false;
            })
            .addCase(fetchQuizQuestion.pending, (state: QuizState) => {
                state.isLoading = true;
            })
            .addCase(
                fetchQuizQuestion.fulfilled,
                (state: QuizState, action: PayloadAction<QuizState>) => {
                    const updatedQuiz = action.payload.quizzes[0];
                    const index = state.quizzes.findIndex((q) => q.id === updatedQuiz.id);
                    if (index !== -1) {
                        state.quizzes[index] = updatedQuiz;
                    } else {
                        state.quizzes.push(updatedQuiz);
                    }
                    state.isLoading = false;
                },
            )
            .addCase(fetchQuizQuestion.rejected, (state: QuizState) => {
                state.isLoading = false;
            });
    },
});

export const { reset } = quizSlice.actions;

export default quizSlice;
