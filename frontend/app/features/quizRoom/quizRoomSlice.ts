import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, { type QuizRoom, type QuizRoomState } from './state/Initial';
import { createQuizRoom } from '~/features/quizRoom/reducers/createQuizRoom';
import { fetchQuizRoomList } from '~/features/quizRoom/reducers/fetchQuizRoomList';
import { joinQuizRoom } from '~/features/quizRoom/reducers/joinQuizRoom';
import type { Participant } from '~/features/quizRoom/state/QuizRoomListResponse';
import { fetchActiveQuizRoom } from '~/features/quizRoom/reducers/fetchActiveQuizRoom';
import { startQuizRoom } from '~/features/quizRoom/reducers/startQuizRoom';
import { deleteQuizRoom } from '~/features/quizRoom/reducers/deleteQuizRoom';
import { deleteParticipant } from '~/features/quizRoom/reducers/deleteParticipant';
import type QuizRoomDeleteParticipantAttributes from '~/features/quizRoom/state/QuizRoomDeleteParticipantAttributes';

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
        addRoomToRoomList(
            state: QuizRoomState,
            action: PayloadAction<{ quizRoom: QuizRoom }>,
        ): void {
            state.rooms.push(action.payload.quizRoom);
        },
        removeRoomFromRoomList(
            state: QuizRoomState,
            action: PayloadAction<string>,
        ): void {
            state.rooms = state.rooms.filter(
                room => room.id !== action.payload,
            );
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
            .addCase(fetchActiveQuizRoom.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(
                fetchActiveQuizRoom.fulfilled,
                (state: QuizRoomState, action: PayloadAction<QuizRoomState>) => {
                    state.currentRoom.id = action.payload.currentRoom.id;
                    state.currentRoom.quiz.title = action.payload.currentRoom.quiz.title;
                    state.currentRoom.host.userName = action.payload.currentRoom.host.userName;
                    state.currentRoom.host.id = action.payload.currentRoom.host.id;
                    state.currentRoom.status = action.payload.currentRoom.status;
                    state.currentRoom.participants = action.payload.currentRoom.participants;
                    state.isLoading = false;
                },
            )
            .addCase(fetchActiveQuizRoom.rejected, (state: QuizRoomState) => {
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
            .addCase(startQuizRoom.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(startQuizRoom.fulfilled, (state: QuizRoomState) => {
                state.currentRoom.status = 'started';
                state.isLoading = false;
            })
            .addCase(startQuizRoom.rejected, (state: QuizRoomState) => {
                state.isLoading = false;
            })
            .addCase(deleteQuizRoom.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(deleteQuizRoom.fulfilled, (state: QuizRoomState) => {
                state.currentRoom.id = '';
                state.currentRoom.quiz.title = '';
                state.currentRoom.host.userName = '';
                state.currentRoom.host.id = '';
                state.currentRoom.status = '';
                state.currentRoom.participants = [];
                state.rooms = [];
                state.isLoading = false;
            })
            .addCase(deleteQuizRoom.rejected, (state: QuizRoomState) => {
                state.isLoading = false;
            })
            .addCase(deleteParticipant.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(
                deleteParticipant.fulfilled,
                (
                    state: QuizRoomState,
                    action: PayloadAction<QuizRoomDeleteParticipantAttributes>,
                ) => {
                    const { quizRoomId, participantId } = action.payload;
                    if (state.currentRoom.id === quizRoomId) {
                        state.currentRoom.participants = state.currentRoom.participants.filter(
                            (participant) => participant.userId !== participantId,
                        );
                    }

                    state.isLoading = false;
                },
            )
            .addCase(deleteParticipant.rejected, (state: QuizRoomState) => {
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

export const { resetRooms, resetCurrentRoom, updateParticipants, addRoomToRoomList, removeRoomFromRoomList } =
    quizRoomSlice.actions;

export default quizRoomSlice;
