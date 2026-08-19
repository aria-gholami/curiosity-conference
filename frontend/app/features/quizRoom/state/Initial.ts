import type { Participant } from '~/features/quizRoom/state/QuizRoomListResponse';

export interface QuizRoom {
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
    selectedAnswerId?: string;
}

export interface QuizRoomState {
    rooms: QuizRoom[];
    currentRoom: QuizRoom;
    isLoading: boolean;
    isRevealing?: boolean;
}

const initialState: QuizRoomState = {
    rooms: [],
    currentRoom: {
        id: '',
        title: '',
        quiz: {
            title: '',
        },
        host: {
            id: '',
            userName: '',
        },
        status: '',
        participants: [],
        selectedAnswerId: '',
    },
    isLoading: false,
    isRevealing: false,
};

export default initialState;
