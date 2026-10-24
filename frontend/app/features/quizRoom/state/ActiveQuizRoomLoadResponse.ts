import type { Participant } from '~/features/quizRoom/state/Initial';

export interface ActiveQuizRoomLoadResponse {
    id: string;
    title: string;
    host: {
        id: string;
        userName: string;
    };
    status: string;
    participants: Participant[];
}
