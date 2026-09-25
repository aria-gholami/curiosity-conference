import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { useEffect } from 'react';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { fetchHighscoreByAllQuizRoom } from '~/features/quizRoom/reducers/fetchHighscoreByAllQuizRoom';

export const useFetchHighscoreByAllQuizRoom = () => {
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);

    useEffect(() => {
        if (quizRoomState.rooms.length > 0) {
            dispatch(fetchHighscoreByAllQuizRoom() as any);
        }
    }, [quizRoomState.rooms.length]);
};
