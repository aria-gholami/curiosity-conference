import {
    addRoomToRoomList,
    removeRoomFromRoomList,
    updateQuizRoomStatus,
} from '~/features/quizRoom/quizRoomSlice';

export default function handleUpdateRoomList(
    eventSource: EventSource | null,
    url: URL,
    store: any,
) {
    if (eventSource) {
        eventSource.close();
    }

    url.searchParams.append('topic', 'quiz/room/list');
    eventSource = new EventSource(url);
    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.id && data.action === 'add') {
            store.dispatch(addRoomToRoomList({ quizRoom: data }));
        }
        if (data.action === 'remove') {
            store.dispatch(removeRoomFromRoomList(data.quizRoomId) as any);
        }
        if (data.action === 'status') {
            store.dispatch(updateQuizRoomStatus({ action: data.status, roomId: data.quizRoomId }));
        }
    };

    return eventSource;
}
