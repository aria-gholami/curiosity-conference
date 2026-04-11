import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { useEffect } from 'react';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { fetchQuizRoomList } from '~/features/quizRoom/reducers/fetchQuizRoomList';

export const useFetchQuizRoomList = () => {
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);

    useEffect(() => {
        dispatch(fetchQuizRoomList() as any);
    }, [quizRoomState]);
};
