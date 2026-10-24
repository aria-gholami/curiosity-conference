import type { Participant } from '~/features/quizRoom/state/Initial';

export interface QuizRoomCreateResponse {
    id: string;
    host: {
        id: string;
        userName: string;
    };
    status: string;
    participants: Participant[];
}
