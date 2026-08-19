import { setIsRevealing } from '~/features/quizRoom/quizRoomSlice';
import { continueOrFinishQuiz } from '~/features/quizRoom/reducers/continueOrFinishQuiz';

export default function handleRevealAnswer(
    eventSource: EventSource | null,
    url: URL,
    roomId: string,
    store: any,
) {
    if (eventSource) {
        eventSource.close();
    }

    url.searchParams.append('topic', `quiz/room/${roomId}/answer/reveal`);
    eventSource = new EventSource(url);
    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        const state = store.getState();
        const userId = state.user.userId;
        store.dispatch(setIsRevealing(true));
        setTimeout(() => {
            store.dispatch(setIsRevealing(false));
            if (userId === data.hostId) {
                store.dispatch(continueOrFinishQuiz(roomId));
            }
        }, 3000);
    };

    return eventSource;
}
