import { removeCurrentQuestion } from '~/features/quiz/quizSlice';
import { setSelectedAnswerId, updateQuizRoomStatus } from '~/features/quizRoom/quizRoomSlice';

export default function handleUpdateQuizQuestionContinue(
    eventSource: EventSource | null,
    url: URL,
    roomId: string,
    store: any,
) {
    if (eventSource) {
        eventSource.close();
    }

    url.searchParams.append('topic', `trivia/quiz/room/${roomId}/continue`);
    eventSource = new EventSource(url);
    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        store.dispatch(setSelectedAnswerId(''));
        store.dispatch(removeCurrentQuestion(roomId));
        store.dispatch(updateQuizRoomStatus({ action: data.status, roomId }));
    };

    return eventSource;
}
