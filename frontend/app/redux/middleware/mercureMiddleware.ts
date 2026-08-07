import type { Middleware } from '@reduxjs/toolkit';
import { joinQuizRoom } from '~/features/quizRoom/reducers/joinQuizRoom';
import { createQuizRoom } from '~/features/quizRoom/reducers/createQuizRoom';
import { fetchQuizRoomList } from '~/features/quizRoom/reducers/fetchQuizRoomList';
import { fetchActiveQuizRoom } from '~/features/quizRoom/reducers/fetchActiveQuizRoom';
import handleUpdateParticipants from '~/redux/middleware/utils/mercure/handleUpdateParticipants';
import handleUpdateRoomList from '~/redux/middleware/utils/mercure/handleUpdateRoomList';
import handleUpdateQuizStatus from '~/redux/middleware/utils/mercure/handleUpdateQuizStatus';
import handleUpdateQuizQuestionContinue from '~/redux/middleware/utils/mercure/handleUpdateQuizQuestionContinue';

let participantsEventSource: EventSource | null = null;
let quizRoomQuestionContinueEventSource: EventSource | null = null;
let roomListEventSource: EventSource | null = null;
let quizStatusEventSource: EventSource | null = null;
const mercureUrl = import.meta.env.VITE_MERCURE_URL;

export const mercureMiddleware: Middleware = (store) => (next) => (action) => {
    const result = next(action);

    const roomWasLoaded =
        joinQuizRoom.fulfilled.match(action) ||
        createQuizRoom.fulfilled.match(action) ||
        fetchActiveQuizRoom.fulfilled.match(action);

    if (roomWasLoaded) {
        const roomId = action.payload.currentRoom.id;

        participantsEventSource = handleUpdateParticipants(
            participantsEventSource,
            new URL(mercureUrl),
            roomId,
            store,
        );

        quizStatusEventSource = handleUpdateQuizStatus(
            quizStatusEventSource,
            new URL(mercureUrl),
            roomId,
            store,
        );

        quizRoomQuestionContinueEventSource = handleUpdateQuizQuestionContinue(
            quizRoomQuestionContinueEventSource,
            new URL(mercureUrl),
            roomId,
            store,
        );
    }

    if (fetchQuizRoomList.fulfilled.match(action)) {
        roomListEventSource = handleUpdateRoomList(roomListEventSource, new URL(mercureUrl), store);
    }

    return result;
};
