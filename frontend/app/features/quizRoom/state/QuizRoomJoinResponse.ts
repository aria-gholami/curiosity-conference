import type { Participant } from '~/features/quizRoom/state/QuizRoomListResponse';

export interface QuizRoomJoinResponse {
    id: string;
    quiz: {
        title: string;
        description: string;
        category: string;
    };
    host: {
        userId: string;
        userName: string;
    };
    status: string;
    participants: Participant[];
}
