interface Answers {
    id: string;
    title: string;
}

export interface Questions {
    id: string;
    title: string;
    answers: Answers[];
}

export interface QuestionnaireState {
    questions: Questions[];
    isLoading: boolean;
}

const initialState: QuestionnaireState = {
    questions: [],
    isLoading: false,
};

export default initialState;
