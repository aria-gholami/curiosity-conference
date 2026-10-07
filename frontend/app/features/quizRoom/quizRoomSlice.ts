import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, { type Participant, type QuizRoom, type QuizRoomState } from './state/Initial';
import { createQuizRoom } from '~/features/quizRoom/reducers/createQuizRoom';
import { fetchQuizRoomList } from '~/features/quizRoom/reducers/fetchQuizRoomList';
import { joinQuizRoom } from '~/features/quizRoom/reducers/joinQuizRoom';
import { fetchActiveQuizRoom } from '~/features/quizRoom/reducers/fetchActiveQuizRoom';
import { startQuizRoom } from '~/features/quizRoom/reducers/startQuizRoom';
import { deleteQuizRoom } from '~/features/quizRoom/reducers/deleteQuizRoom';
import { deleteParticipant } from '~/features/quizRoom/reducers/deleteParticipant';
import type QuizRoomDeleteParticipantAttributes from '~/features/quizRoom/state/QuizRoomDeleteParticipantAttributes';
import { continueOrFinishQuiz } from '~/features/quizRoom/reducers/continueOrFinishQuiz';
import { revealAnswer } from '~/features/quizRoom/reducers/revealAnswer';
import { fetchHighscoreByQuizRoom } from '~/features/quizRoom/reducers/fetchHighscoreByQuizRoom';
import { closeQuizRoom } from '~/features/quizRoom/reducers/closeQuizRoom';
import { fetchHighscoreByAllQuizRoom } from '~/features/quizRoom/reducers/fetchHighscoreByAllQuizRoom';

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
            state.currentRoom.title = '';
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
        removeParticipant(
            state: QuizRoomState,
            action: PayloadAction<{ participantId: string; currentUserId?: string }>,
        ): void {
            state.currentRoom.participants = state.currentRoom.participants.filter(
                (participant) => {
                    return participant.userId !== action.payload.participantId;
                },
            );

            if (
                action.payload.currentUserId &&
                action.payload.participantId === action.payload.currentUserId
            ) {
                state.currentRoom.id = '';
                state.currentRoom.title = '';
                state.currentRoom.quiz.title = '';
                state.currentRoom.host.userName = '';
                state.currentRoom.host.id = '';
                state.currentRoom.status = '';
                state.currentRoom.participants = [];
                state.isLoading = false;
            }
        },
        addRoomToRoomList(
            state: QuizRoomState,
            action: PayloadAction<{ quizRoom: QuizRoom }>,
        ): void {
            state.rooms.push(action.payload.quizRoom);
        },
        removeRoomFromRoomList(state: QuizRoomState, action: PayloadAction<string[]>): void {
            state.rooms = state.rooms.filter((room) => !action.payload.includes(room.id));
            if (action.payload.includes(state.currentRoom.id)) {
                state.currentRoom.id = '';
                state.currentRoom.title = '';
                state.currentRoom.quiz.title = '';
                state.currentRoom.host.userName = '';
                state.currentRoom.host.id = '';
                state.currentRoom.status = '';
                state.currentRoom.participants = [];
                state.isLoading = false;
            }
        },
        updateQuizRoomStatus(
            state: QuizRoomState,
            action: PayloadAction<{ action: string; roomId: string }>,
        ): void {
            state.currentRoom.status = action.payload.action;
            const roomIndex = state.rooms.findIndex((room) => room.id === action.payload.roomId);
            if (roomIndex !== -1) {
                state.rooms[roomIndex].status = action.payload.action;
            }
        },
        sortQuizRoomByStatus(state: QuizRoomState): void {
            const statusOrder: Record<string, number> = {
                created: 1,
                started: 1,
                finished: 1,
                closed: 0,
            };
            state.rooms.sort((a, b) => {
                const statusDifference =
                    (statusOrder[a.status] ?? 999) - (statusOrder[b.status] ?? 999);

                if (statusDifference !== 0) {
                    return statusDifference;
                }

                return (b.score ?? 0) - (a.score ?? 0);
            });
        },
        setSelectedAnswerId(state: QuizRoomState, action: PayloadAction<string>): void {
            state.currentRoom.selectedAnswerId = action.payload;
        },
        setIsRevealing(state: QuizRoomState, action: PayloadAction<boolean>): void {
            state.isRevealing = action.payload;
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
                    state.currentRoom.title = action.payload.currentRoom.title;
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
                state.currentRoom.title = '';
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
                    state.currentRoom.title = action.payload.currentRoom.title;
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
                state.currentRoom.title = '';
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
                    state.currentRoom.title = action.payload.currentRoom.title;
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
                state.currentRoom.title = '';
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
                state.currentRoom.title = '';
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
            })
            .addCase(continueOrFinishQuiz.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(continueOrFinishQuiz.fulfilled, (state: QuizRoomState) => {
                state.isLoading = false;
            })
            .addCase(continueOrFinishQuiz.rejected, (state: QuizRoomState) => {
                state.isLoading = false;
            })
            .addCase(revealAnswer.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(revealAnswer.fulfilled, (state: QuizRoomState) => {
                state.isLoading = false;
            })
            .addCase(revealAnswer.rejected, (state: QuizRoomState) => {
                state.isLoading = false;
            })
            .addCase(closeQuizRoom.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(closeQuizRoom.fulfilled, (state: QuizRoomState) => {
                state.currentRoom.status = 'closed';
                state.isLoading = false;
            })
            .addCase(closeQuizRoom.rejected, (state: QuizRoomState) => {
                state.isLoading = false;
            })
            .addCase(fetchHighscoreByQuizRoom.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(
                fetchHighscoreByQuizRoom.fulfilled,
                (state: QuizRoomState, action: PayloadAction<QuizRoomState>) => {
                    state.currentRoom.participants = action.payload.currentRoom.participants;
                    state.rooms = action.payload.rooms;
                    state.isLoading = false;
                },
            )
            .addCase(fetchHighscoreByQuizRoom.rejected, (state: QuizRoomState) => {
                state.isLoading = false;
            })
            .addCase(fetchHighscoreByAllQuizRoom.pending, (state: QuizRoomState) => {
                state.isLoading = true;
            })
            .addCase(
                fetchHighscoreByAllQuizRoom.fulfilled,
                (state: QuizRoomState, action: PayloadAction<QuizRoomState>) => {
                    state.currentRoom = action.payload.currentRoom;
                    state.rooms = action.payload.rooms;
                    state.isLoading = false;
                },
            )
            .addCase(fetchHighscoreByAllQuizRoom.rejected, (state: QuizRoomState) => {
                state.isLoading = false;
            });
    },
});

export const {
    resetRooms,
    resetCurrentRoom,
    updateParticipants,
    removeParticipant,
    addRoomToRoomList,
    removeRoomFromRoomList,
    updateQuizRoomStatus,
    setSelectedAnswerId,
    setIsRevealing,
    sortQuizRoomByStatus,
} = quizRoomSlice.actions;

export default quizRoomSlice;
