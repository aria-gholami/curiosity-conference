import type { Middleware } from '@reduxjs/toolkit';
import { joinQuizRoom } from '~/features/quizRoom/reducers/joinQuizRoom';
import handleUpdateParticipants from '~/redux/middleware/utils/mercure/handleUpdateParticipants';
import { createQuizRoom } from '~/features/quizRoom/reducers/createQuizRoom';
import handleUpdateRoomList from '~/redux/middleware/utils/mercure/handleUpdateRoomList';
import { fetchQuizRoomList } from '~/features/quizRoom/reducers/fetchQuizRoomList';

let eventSource: EventSource | null = null;
const url = new URL(import.meta.env.VITE_MERCURE_URL);

export const mercureMiddleware: Middleware = (store) => (next) => (action) => {
    const result = next(action);
    if (joinQuizRoom.fulfilled.match(action) || createQuizRoom.fulfilled.match(action)) {
        handleUpdateParticipants(eventSource, url, action.payload.currentRoom.id, store);
    }

    if (fetchQuizRoomList.fulfilled.match(action)) {
        handleUpdateRoomList(eventSource, url, store);
    }

    return result;
};
