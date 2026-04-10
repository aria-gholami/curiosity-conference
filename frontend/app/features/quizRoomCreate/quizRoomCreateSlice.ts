import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, { type QuizRoomCreateState } from './state/Initial';
import { createQuizRoom } from '~/features/quizRoomCreate/reducers/createQuizRoom';

const quizRoomCreateSlice = createSlice({
    name: 'quizRoomCreate',
    initialState,
    reducers: {
        reset(state: QuizRoomCreateState): void {
            state.id = '';
            state.quizId = '';
            state.hostId = '';
            state.isLoading = false;
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(createQuizRoom.pending, (state: QuizRoomCreateState) => {
                state.isLoading = true;
            })
            .addCase(
                createQuizRoom.fulfilled,
                (state: QuizRoomCreateState, action: PayloadAction<QuizRoomCreateState>) => {
                    state.id = action.payload.id;
                    state.quizId = action.payload.quizId;
                    state.hostId = action.payload.hostId;
                    state.isLoading = false;
                },
            )
            .addCase(createQuizRoom.rejected, (state: QuizRoomCreateState) => {
                state.id = '';
                state.quizId = '';
                state.hostId = '';
                state.isLoading = false;
            });
    },
});

export const { reset } = quizRoomCreateSlice.actions;

export default quizRoomCreateSlice;
