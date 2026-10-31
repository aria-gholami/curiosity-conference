export interface Question {
    id: string;
    title: string;
}

interface Answer {
    id: string;
    title: string;
}

interface QuestionnaireQuestionData {
    question: Question;
    answer: Answer;
}

export default interface QuestionnaireQuestionsResponse {
    data: QuestionnaireQuestionData[];
}
