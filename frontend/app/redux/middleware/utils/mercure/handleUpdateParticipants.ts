import {
    removeParticipant,
    removeRoomFromRoomList,
    updateParticipants,
} from '~/features/quizRoom/quizRoomSlice';

export default function handleUpdateParticipants(
    eventSource: EventSource | null,
    url: URL,
    roomId: string,
    store: any,
) {
    if (eventSource) {
        eventSource.close();
    }

    url.searchParams.append('topic', `trivia/quiz/room/${roomId}/participants`);
    eventSource = new EventSource(url);
    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.action === 'join' && data.participants) {
            store.dispatch(updateParticipants({ participants: data.participants }));
        }
        if (data.action === 'remove' && data.participantId) {
            const currentUserId = store.getState().user.userId;
            store.dispatch(
                removeParticipant({
                    participantId: data.participantId,
                    currentUserId: currentUserId,
                }),
            );
        }
        if (data.action === 'removeQuizRoom' && data.quizRoomId) {
            store.dispatch(removeRoomFromRoomList(data.quizRoomId));
        }
    };

    return eventSource;
}
