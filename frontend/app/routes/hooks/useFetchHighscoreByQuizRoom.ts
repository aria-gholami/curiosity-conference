import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { useEffect } from 'react';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { fetchHighscoreByQuizRoom } from '~/features/quizRoom/reducers/fetchHighscoreByQuizRoom';

export const useFetchHighscoreByQuizRoom = () => {
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);

    useEffect(() => {
        if (quizRoomState.currentRoom.id) {
            dispatch(fetchHighscoreByQuizRoom(quizRoomState.currentRoom.id) as any);
        }
    }, [quizRoomState.currentRoom.id]);
};
