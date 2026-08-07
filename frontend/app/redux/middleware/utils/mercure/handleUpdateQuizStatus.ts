import { updateQuizRoomStatus } from '~/features/quizRoom/quizRoomSlice';

export default function handleUpdateQuizStatus(
    eventSource: EventSource | null,
    url: URL,
    roomId: string,
    store: any,
) {
    if (eventSource) {
        eventSource.close();
    }

    url.searchParams.append('topic', `quiz/room/${roomId}/status`);
    eventSource = new EventSource(url);
    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.action === 'started') {
            store.dispatch(updateQuizRoomStatus({ action: data.action, roomId }));
        }
    };

    return eventSource;
}
