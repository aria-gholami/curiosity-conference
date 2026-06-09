import type { Participant } from '~/features/quizRoom/state/QuizRoomListResponse';

export interface ActiveQuizRoomLoadResponse {
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
