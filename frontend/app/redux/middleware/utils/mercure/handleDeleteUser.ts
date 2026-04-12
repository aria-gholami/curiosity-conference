import {resetRooms} from "~/features/quizRoom/quizRoomSlice";

export default function handleDeleteUser(
    eventSource: EventSource | null,
    url: URL,
    store: any,
) {
    if (typeof window === 'undefined') {
        return;
    }
    if (eventSource) {
        eventSource.close();
    }

    url.searchParams.append('topic', 'user/delete');
    eventSource = new EventSource(url);
    eventSource.onmessage = () => {
        store.dispatch(resetRooms() as any);
    };
}
