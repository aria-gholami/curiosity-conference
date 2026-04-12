import type { Middleware } from '@reduxjs/toolkit';
import { resetCurrentRoom } from '~/features/quizRoom/quizRoomSlice';
import { joinQuizRoom } from '~/features/quizRoom/reducers/joinQuizRoom';
import handleUpdateParticipants from '~/redux/middleware/utils/mercure/handleUpdateParticipants';

let eventSource: EventSource | null = null;
const url = new URL(import.meta.env.VITE_MERCURE_URL);

export const mercureMiddleware: Middleware = (store) => (next) => (action) => {
    const result = next(action);
    if (joinQuizRoom.fulfilled.match(action)) {
        handleUpdateParticipants(eventSource, url, action.payload.currentRoom.id, store);
    }

    if (resetCurrentRoom.match(action)) {
        if (eventSource) {
            eventSource.close();
            eventSource = null;
        }
    }

    return result;
};
