import type { Participant } from '~/features/quizRoom/state/Initial';

export interface QuizRoomListItem {
    id: string;
    title: string;
    host: {
        id: string;
        userName: string;
    };
    status: string;
    participants: Participant[];
}

export default interface QuizRoomListResponse {
    data: QuizRoomListItem[];
}
