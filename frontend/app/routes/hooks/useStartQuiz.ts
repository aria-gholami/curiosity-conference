import { useAppSelector } from '~/redux/hooks';
import { useEffect } from 'react';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useNavigate } from 'react-router';
import type { UserState } from '~/features/user/state/Initial';

export const useStartQuiz = () => {
    const navigate = useNavigate();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);

    useEffect(() => {
        if (quizRoomState.currentRoom.status === 'started') {
            navigate('/trivia/quiz');
        }
    }, [quizRoomState]);
};
