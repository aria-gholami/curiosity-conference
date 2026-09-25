export interface Participant {
    userName: string;
    userId: string;
    score: number;
}

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
    score?: number;
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
