export interface QuizRoomCreateState {
    id: string;
    quizId: string;
    hostId: string;
    isLoading: boolean;
}

const initialState: QuizRoomCreateState = {
    id: '',
    quizId: '',
    hostId: '',
    isLoading: false,
};

export default initialState;
