import { setIsRevealing } from '~/features/quizRoom/quizRoomSlice';
import { continueOrFinishQuiz } from '~/features/quizRoom/reducers/continueOrFinishQuiz';
import type HighscoreUpdatePayload from '~/features/quizRoom/state/HighscoreUpdatePayload';
import { updateHighscore } from '~/features/quizRoom/reducers/updateHighscore';

export default function handleRevealAnswer(
    eventSource: EventSource | null,
    url: URL,
    roomId: string,
    store: any,
) {
    if (eventSource) {
        eventSource.close();
    }

    url.searchParams.append('topic', `trivia/quiz/room/${roomId}/answer/reveal`);
    eventSource = new EventSource(url);
    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        store.dispatch(setIsRevealing(true));
        setTimeout(() => {
            const state = store.getState();
            const userId = state.user.userId;
            store.dispatch(setIsRevealing(false));
            if (userId === data.hostId) {
                const updateHighscorePayload: HighscoreUpdatePayload = {
                    quizRoomId: roomId,
                    answerId: state.quizRoom.currentRoom.selectedAnswerId,
                };
                store.dispatch(updateHighscore(updateHighscorePayload));
                store.dispatch(continueOrFinishQuiz(roomId));
            }
        }, 3000);
    };

    return eventSource;
}
