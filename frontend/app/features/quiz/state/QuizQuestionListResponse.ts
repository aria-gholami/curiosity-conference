interface Answers {
    id: string;
    title: string;
    isCorrect: boolean;
}

export interface QuizRoomQuestionListResponse {
    question: string;
    answers: Answers[];
}
