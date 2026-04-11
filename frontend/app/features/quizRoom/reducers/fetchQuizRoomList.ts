import { createAsyncThunk } from '@reduxjs/toolkit';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import type { RootState } from '~/redux/store';
import type QuizRoomListResponse from '~/features/quizRoom/state/QuizRoomListResponse';
import type { QuizRoomListItem } from '~/features/quizRoom/state/QuizRoomListResponse';

export const fetchQuizRoomList = createAsyncThunk<QuizRoomState, void, { state: RootState }>(
    'quizRoomList/fetch',
    async () => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/quiz/room`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
            });
            if (!response.ok) {
                throw new Error('Response by fetch quiz room list api is invalid.');
            }

            const responseBody: QuizRoomListResponse = await response.json();
            const state: QuizRoomState = {
                currentRoom: {
                    id: '',
                    quiz: {
                        title: '',
                        description: '',
                        category: '',
                    },
                    host: {
                        userId: '',
                        userName: '',
                    },
                    status: '',
                    participants: [],
                },
                rooms: responseBody.data.map((room: QuizRoomListItem) => ({
                    id: room.id,
                    quiz: {
                        title: room.quiz.title,
                        description: room.quiz.description,
                        category: room.quiz.category,
                    },
                    host: {
                        userId: room.host.userId,
                        userName: room.host.userName,
                    },
                    status: room.status,
                    participants: room.participants,
                })),
                isLoading: true,
            };

            return state;
        } catch (error) {
            console.error(error);

            throw error;
        }
    },
    {
        condition: (_arg, { getState }) => {
            const { quizRoom } = getState() as RootState;
            if (quizRoom.isLoading || quizRoom.rooms.length !== 0) {
                return false;
            }
        },
    },
);
