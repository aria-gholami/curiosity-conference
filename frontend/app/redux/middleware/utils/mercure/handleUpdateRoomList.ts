import {
    addRoomToRoomList,
    removeRoomFromRoomList,
    removeRoomParticipant,
    sortQuizRoomByStatus,
    updateQuizRoomStatus,
    updateRoomParticipants,
} from '~/features/quizRoom/quizRoomSlice';

export default function handleUpdateRoomList(
    eventSource: EventSource | null,
    url: URL,
    store: any,
) {
    if (eventSource) {
        eventSource.close();
    }

    url.searchParams.append('topic', 'trivia/quiz/room/list');
    eventSource = new EventSource(url);
    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (data.id && data.action === 'add') {
            store.dispatch(addRoomToRoomList({ quizRoom: data }));
        }
        if (data.action === 'remove') {
            store.dispatch(removeRoomFromRoomList(data.quizRoomResults) as any);
        }
        if (data.action === 'status') {
            store.dispatch(updateQuizRoomStatus({ action: data.status, roomId: data.quizRoomId }));
            store.dispatch(sortQuizRoomByStatus());
        }
        if (data.action === 'updateParticipants' && data.quizRoomId && data.participants) {
            store.dispatch(
                updateRoomParticipants({
                    quizRoomId: data.quizRoomId,
                    participants: data.participants,
                }),
            );
        }
        if (data.action === 'removeParticipant' && data.quizRoomId && data.participantId) {
            store.dispatch(
                removeRoomParticipant({
                    quizRoomId: data.quizRoomId,
                    participantId: data.participantId,
                }),
            );
        }
    };

    return eventSource;
}
