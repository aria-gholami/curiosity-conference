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
    roomId: string;
    question?: QuizQuestion;
}

export interface QuizState {
    quiz: Quiz;
    isLoading: boolean;
}

const initialState: QuizState = {
    quiz: {
        roomId: '',
    },
    isLoading: false,
};

export default initialState;
