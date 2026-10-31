import type { ConfirmationAction } from '~/components/trivia/types/ConfirmationAction';

interface Question {
    question: string;
    answers: {
        id: string;
        title: string;
        isCorrect: boolean;
    }[];
}

export default interface TriviaQuizQuestionProps {
    currentQuestion: Question;
    setConfirmation: (confirmationAction: ConfirmationAction) => void;
}
