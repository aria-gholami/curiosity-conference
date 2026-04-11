export interface Participant {
    userName: string;
    userId: string;
}

export interface QuizRoomListItem {
    id: string;
    quiz: {
        title: string;
        description: string;
        category: string;
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
