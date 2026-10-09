import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { useEffect } from 'react';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { fetchQuizQuestion } from '~/features/quiz/reducers/fetchQuizQuestion';
import type { QuizState } from '~/features/quiz/state/Initial';

export const useFetchQuizRoomQuestion = (setCurrentQuestion: any) => {
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const quizState: QuizState = useAppSelector((state) => state.quiz);

    useEffect(() => {
        console.warn('useFetchQuizRoomQuestion');
        if (quizRoomState.currentRoom.status === 'started') {
            dispatch(fetchQuizQuestion(quizRoomState.currentRoom.id) as any);
        }
        const existingQuiz = quizState.quizzes.find((q) => q.id === quizRoomState.currentRoom.id);
        if (existingQuiz) {
            setCurrentQuestion({
                question: existingQuiz.question?.title ?? 'Loading...',
                answers: existingQuiz.question?.answers ?? [],
            });
        }
    }, [quizState.quizzes, quizRoomState.currentRoom.id, quizRoomState.currentRoom.status]);
};
