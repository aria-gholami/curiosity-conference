import type { Participant } from '~/features/quizRoom/state/Initial';

export interface QuizRoomJoinResponse {
    id: string;
    title: string;
    quiz: {
        title: string;
    };
    host: {
        id: string;
        userName: string;
    };
    status: string;
    participants: Participant[];
}
