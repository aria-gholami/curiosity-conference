export interface Participant {
    userName: string;
    userId: string;
}

export interface QuizRoomListItem {
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

export default interface QuizRoomListResponse {
    data: QuizRoomListItem[];
}
