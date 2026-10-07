import { useEffect } from 'react';
import { reset } from '~/features/quiz/quizSlice';
import { useAppDispatch } from '~/redux/hooks';

export const useResetQuiz = () => {
    const dispatch = useAppDispatch();

    useEffect(() => {
        dispatch(reset() as any);
    }, []);
};
