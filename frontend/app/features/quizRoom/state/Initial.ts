import type { Participant } from '~/features/quizRoom/state/QuizRoomListResponse';

export interface QuizRoom {
    id: string;
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
        },
        host: {
            id: '',
            userName: '',
        },
        status: '',
        participants: [],
    },
    isLoading: false,
};

export default initialState;
