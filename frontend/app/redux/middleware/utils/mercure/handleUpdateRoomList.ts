import { addRoomToRoomList, resetRooms } from '~/features/quizRoom/quizRoomSlice';

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
            store.dispatch(resetRooms() as any);
        }
    };
}
