import { removeCurrentQuestion } from '~/features/quiz/quizSlice';
import type HighscoreUpdatePayload from '~/features/quizRoom/state/HighscoreUpdatePayload';
import { updateHighscore } from '~/features/quizRoom/reducers/updateHighscore';
import { setSelectedAnswerId } from '~/features/quizRoom/quizRoomSlice';

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
        const state = store.getState();
        const userId = state.user.userId;
        const selectedAnswerId = state.quizRoom.currentRoom.selectedAnswerId;
        const updateHighscorePayload: HighscoreUpdatePayload = {
            quizRoomId: roomId,
            userId: userId,
            answerId: selectedAnswerId,
        };
        store.dispatch(updateHighscore(updateHighscorePayload));
        store.dispatch(setSelectedAnswerId(''));
        store.dispatch(removeCurrentQuestion(roomId));
    };

    return eventSource;
}
