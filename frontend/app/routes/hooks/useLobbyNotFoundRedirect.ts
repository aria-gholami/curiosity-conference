import { useAppSelector } from '~/redux/hooks';
import { useEffect } from 'react';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useNavigate } from 'react-router';
import type { UserState } from '~/features/user/state/Initial';

export const useLobbyNotFoundRedirect = () => {
    const navigate = useNavigate();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);

    useEffect(() => {
        if (quizRoomState.currentRoom.id === '') {
            navigate('/');
        }
    }, [quizRoomState, userState]);
};
