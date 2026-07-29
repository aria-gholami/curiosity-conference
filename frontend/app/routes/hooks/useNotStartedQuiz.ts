import { useAppSelector } from '~/redux/hooks';
import { useEffect } from 'react';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useNavigate } from 'react-router';

export const useNotStartedQuiz = () => {
    const navigate = useNavigate();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);

    useEffect(() => {
        if (quizRoomState.currentRoom.status !== 'started') {
            navigate('/trivia/list');
        }
    }, [quizRoomState]);
};
