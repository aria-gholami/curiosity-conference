interface Answers {
    id: string;
    title: string;
    isCorrect: boolean;
}

interface QuizQuestion {
    title: string;
    answers: Answers[];
}

export interface Quiz {
    id: string;
    title: string;
    description: string;
    category: string;
    question?: QuizQuestion;
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
