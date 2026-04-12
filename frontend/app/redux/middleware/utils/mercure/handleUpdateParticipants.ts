import { updateParticipants } from '~/features/quizRoom/quizRoomSlice';

export default function handleUpdateParticipants(
    eventSource: EventSource | null,
    url: URL,
    roomId: string,
    store: any,
) {
    if (eventSource) {
        eventSource.close();
    }

    url.searchParams.append('topic', `quiz/room/${roomId}/participants`);
    eventSource = new EventSource(url);
    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.participants) {
            store.dispatch(updateParticipants({ participants: data.participants }));
        }
    };
}
