import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import { useEffect } from 'react';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { fetchActiveQuizRoom } from '~/features/quizRoom/reducers/fetchActiveQuizRoom';
import type { UserState } from '~/features/user/state/Initial';
import type { ActiveQuizRoomLoadRequestBody } from '~/features/quizRoom/state/ActiveQuizRoomLoadRequestBody';
import { useNavigate } from 'react-router';

export const useFetchActiveQuizRoom = () => {
    const navigate = useNavigate();
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);

    useEffect(() => {
        const requestBody: ActiveQuizRoomLoadRequestBody = {
            userId: userState.userId,
        };
        dispatch(fetchActiveQuizRoom(requestBody) as any);

        if (quizRoomState.currentRoom.id !== '' && quizRoomState.currentRoom.status === 'created') {
            navigate('/trivia/lobby');
        }
    }, [quizRoomState]);
};
