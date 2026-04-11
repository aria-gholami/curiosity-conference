import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { useEffect } from 'react';
import type { QuizState } from '~/features/quiz/state/Initial';
import { fetchQuizList } from '~/features/quiz/reducers/fetchQuizList';

export const useFetchQuizList = () => {
    const dispatch = useAppDispatch();
    const quizState: QuizState = useAppSelector((state) => state.quiz);

    useEffect(() => {
        dispatch(fetchQuizList() as any);
    }, [quizState]);
};
