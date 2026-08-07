import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, { type QuizState } from './state/Initial';
import { fetchQuizList } from '~/features/quiz/reducers/fetchQuizList';
import { fetchQuizQuestion } from '~/features/quiz/reducers/fetchQuizQuestion';
import { continueOrFinishQuiz } from '~/features/quizRoom/reducers/continueOrFinishQuiz';

const quizSlice = createSlice({
    name: 'quizRoom',
    initialState,
    reducers: {
        reset(state: QuizState): void {
            state.quizzes = [];
            state.isLoading = false;
        },
        removeCurrentQuestion(state: QuizState, action): void {
            const quizRoomId = action.payload;
            const index = state.quizzes.findIndex((q) => q.id === quizRoomId);
            if (index !== -1) {
                state.quizzes[index].question = undefined;
            }
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
            })
            .addCase(continueOrFinishQuiz.fulfilled, (state: QuizState, action) => {
                const quizRoomId = action.meta.arg;
                const index = state.quizzes.findIndex((q) => q.id === quizRoomId);
                if (index !== -1) {
                    state.quizzes[index].question = undefined;
                }
            });
    },
});

export const { reset, removeCurrentQuestion } = quizSlice.actions;

export default quizSlice;
