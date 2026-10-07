import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { useEffect } from 'react';
import { fetchHighscoreByQuizRoom } from '~/features/quizRoom/reducers/fetchHighscoreByQuizRoom';

export const useFetchHighscoreByQuizRoom = (id) => {
    const dispatch = useAppDispatch();

    useEffect(() => {
        dispatch(fetchHighscoreByQuizRoom(id) as any);
    }, []);
};
