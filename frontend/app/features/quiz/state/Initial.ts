interface Quiz {
    id: string;
    title: string;
    description: string;
    category: string;
}

export interface QuizState {
    quizzes: Quiz[];
    isLoading: boolean;
}

const initialState: QuizState = {
    quizzes: [],
    isLoading: false,
};

export default initialState;
