import { removeCurrentQuestion } from '~/features/quiz/quizSlice';

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
    eventSource.onmessage = () => {
        store.dispatch(removeCurrentQuestion(roomId));
    };

    return eventSource;
}
