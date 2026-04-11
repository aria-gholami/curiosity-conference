export interface Participant {
    userName: string;
}

export interface QuizRoomListItem {
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

export default interface QuizRoomListResponse {
    data: QuizRoomListItem[];
}
