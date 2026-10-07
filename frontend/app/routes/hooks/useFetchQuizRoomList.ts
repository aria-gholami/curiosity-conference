import { useAppDispatch } from '~/redux/hooks';
import { useEffect } from 'react';
import { fetchQuizRoomList } from '~/features/quizRoom/reducers/fetchQuizRoomList';

export const useFetchQuizRoomList = () => {
    const dispatch = useAppDispatch();

    useEffect(() => {
        dispatch(fetchQuizRoomList() as any);
    });
};
