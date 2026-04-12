import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, { type QuizRoomState } from './state/Initial';
import { createQuizRoom } from '~/features/quizRoom/reducers/createQuizRoom';
import { fetchQuizRoomList } from '~/features/quizRoom/reducers/fetchQuizRoomList';
import { joinQuizRoom } from '~/features/quizRoom/reducers/joinQuizRoom';
import type {Participant} from "~/features/quizRoom/state/QuizRoomListResponse";

const quizRoomSlice = createSlice({
    name: 'quizRoom',
    initialState,
    reducers: {
        resetRooms(state: QuizRoomState): void {
            state.rooms = [];
            state.isLoading = false;
        },
        resetCurrentRoom(state: QuizRoomState): void {
            state.currentRoom.id = '';
            state.currentRoom.quiz.title = '';
            state.currentRoom.host.userName = '';
            state.currentRoom.host.id = '';
            state.currentRoom.status = '';
            state.currentRoom.participants = [];
            state.isLoading = false;
        },
        updateParticipants(
            state: QuizRoomState,
            action: PayloadAction<{ participants: Participant[] }>,
        ): void {
            state.currentRoom.participants = action.payload.participants;
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(createQuizRoom.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(
                createQuizRoom.fulfilled,
                (state: QuizRoomState, action: PayloadAction<QuizRoomState>) => {
                    state.currentRoom.id = action.payload.currentRoom.id;
                    state.currentRoom.quiz.title = action.payload.currentRoom.quiz.title;
                    state.currentRoom.host.userName = action.payload.currentRoom.host.userName;
                    state.currentRoom.host.id = action.payload.currentRoom.host.id;
                    state.currentRoom.status = action.payload.currentRoom.status;
                    state.currentRoom.participants = action.payload.currentRoom.participants;
                    state.rooms = [];
                    state.isLoading = false;
                },
            )
            .addCase(createQuizRoom.rejected, (state: QuizRoomState) => {
                state.currentRoom.id = '';
                state.currentRoom.quiz.title = '';
                state.currentRoom.host.userName = '';
                state.currentRoom.host.id = '';
                state.currentRoom.status = '';
                state.currentRoom.participants = [];
                state.isLoading = false;
            })
            .addCase(joinQuizRoom.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(
                joinQuizRoom.fulfilled,
                (state: QuizRoomState, action: PayloadAction<QuizRoomState>) => {
                    state.currentRoom.id = action.payload.currentRoom.id;
                    state.currentRoom.quiz.title = action.payload.currentRoom.quiz.title;
                    state.currentRoom.host.userName = action.payload.currentRoom.host.userName;
                    state.currentRoom.host.id = action.payload.currentRoom.host.id;
                    state.currentRoom.status = action.payload.currentRoom.status;
                    state.currentRoom.participants = action.payload.currentRoom.participants;
                    state.rooms = [];
                    state.isLoading = false;
                },
            )
            .addCase(joinQuizRoom.rejected, (state: QuizRoomState) => {
                state.currentRoom.id = '';
                state.currentRoom.quiz.title = '';
                state.currentRoom.host.userName = '';
                state.currentRoom.host.id = '';
                state.currentRoom.status = '';
                state.currentRoom.participants = [];
                state.isLoading = false;
            })
            .addCase(fetchQuizRoomList.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(
                fetchQuizRoomList.fulfilled,
                (state: QuizRoomState, action: PayloadAction<QuizRoomState>) => {
                    state.rooms = action.payload.rooms;
                    state.isLoading = false;
                },
            )
            .addCase(fetchQuizRoomList.rejected, (state: QuizRoomState) => {
                state.rooms = [];
                state.isLoading = false;
            });
    },
});

export const { resetRooms, resetCurrentRoom, updateParticipants } = quizRoomSlice.actions;

export default quizRoomSlice;
