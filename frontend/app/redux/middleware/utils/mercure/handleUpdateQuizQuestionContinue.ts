import { removeCurrentQuestion } from '~/features/quiz/quizSlice';
import type HighscoreUpdatePayload from '~/features/quizRoom/state/HighscoreUpdatePayload';
import { updateHighscore } from '~/features/quizRoom/reducers/updateHighscore';
import {
    setSelectedAnswerId,
    setIsRevealing,
    setIsNextQuestionButtonDisabled,
} from '~/features/quizRoom/quizRoomSlice';

export default function handleUpdateQuizQuestionContinue(
    eventSource: EventSource | null,
    url: URL,
    roomId: string,
    store: any,
) {
    if (eventSource) {
        eventSource.close();
    }

    url.searchParams.append('topic', `quiz/room/${roomId}/continue`);
    eventSource = new EventSource(url);
    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        const state = store.getState();
        const userId = state.user.userId;
        if (userId === data.hostId) {
            store.dispatch(setIsNextQuestionButtonDisabled(true));
            setTimeout(() => {
                store.dispatch(setIsNextQuestionButtonDisabled(false));
            }, 3000);

            return;
        }

        store.dispatch(setIsRevealing(true));
        setTimeout(() => {
            const selectedAnswerId = state.quizRoom.currentRoom.selectedAnswerId;

            const updateHighscorePayload: HighscoreUpdatePayload = {
                quizRoomId: roomId,
                userId: userId,
                answerId: selectedAnswerId,
            };
            store.dispatch(updateHighscore(updateHighscorePayload));
            store.dispatch(setSelectedAnswerId(''));
            store.dispatch(removeCurrentQuestion(roomId));
            store.dispatch(setIsRevealing(false));
        }, 3000);
    };

    return eventSource;
}
