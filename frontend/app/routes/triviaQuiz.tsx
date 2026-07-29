import RegistrationPopup from '~/components/registration/registrationPopup';
import { useState } from 'react';
import { useDisplayRegistrationPopup } from '~/routes/hooks/useDisplayRegistrationPopup';
import { useFetchActiveQuizRoom } from '~/routes/hooks/useFetchActiveQuizRoom';
import { useLobbyNotFoundRedirect } from '~/routes/hooks/useLobbyNotFoundRedirect';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import type { UserState } from '~/features/user/state/Initial';

export default function TriviaQuiz() {
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);
    const [isRegistrationPopupOpen, setIsRegistrationPopupOpen] = useState(true);

    useDisplayRegistrationPopup(setIsRegistrationPopupOpen);
    useFetchActiveQuizRoom();
    useLobbyNotFoundRedirect();

    const isHost = userState.userId === quizRoomState.currentRoom.host.id;

    return (
        <div className="relative min-h-screen overflow-hidden font-sans bg-(image:--background-main)">
            <div className="absolute inset-0 bg-(image:--background-overlay)" />
            <div className="absolute -right-32 -top-32 h-96 w-96 rounded-full blur-3xl bg-turquoise-glow" />
            <div className="absolute -left-24 top-1/3 h-72 w-72 rounded-full blur-3xl bg-dark-blue-glow" />
            <div className="absolute bottom-0 right-1/4 h-64 w-64 rounded-full blur-3xl bg-light-blue-glow" />

            <RegistrationPopup open={isRegistrationPopupOpen} />
        </div>
    );
}
