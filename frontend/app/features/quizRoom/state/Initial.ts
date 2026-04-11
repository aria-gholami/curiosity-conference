import type { Participant } from '~/features/quizRoom/state/QuizRoomListResponse';

interface QuizRoom {
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

export interface QuizRoomState {
    rooms: QuizRoom[];
    currentRoom: QuizRoom;
    isLoading: boolean;
}

const initialState: QuizRoomState = {
    rooms: [],
    currentRoom: {
        id: '',
        quiz: {
            title: '',
            description: '',
            category: '',
        },
        host: {
            userId: '',
            userName: '',
        },
        status: '',
        participants: [],
    },
    isLoading: false,
};

export default initialState;
