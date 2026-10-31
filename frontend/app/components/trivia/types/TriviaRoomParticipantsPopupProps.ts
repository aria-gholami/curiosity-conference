import type { QuizRoom } from '~/features/quizRoom/state/Initial';

export default interface TriviaRoomParticipantsPopupProps {
    room: QuizRoom;
    open: boolean;
    onClose: () => void;
}
