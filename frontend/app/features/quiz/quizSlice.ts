import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, { type QuizState } from './state/Initial';
import { fetchQuizList } from '~/features/quiz/reducers/fetchQuizList';

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
            });
    },
});

export const { reset } = quizSlice.actions;

export default quizSlice;
