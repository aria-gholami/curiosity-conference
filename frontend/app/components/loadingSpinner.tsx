import type { QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useAppSelector } from '~/redux/hooks';
import type { UserState } from '~/features/user/state/Initial';
import type { QuizState } from '~/features/quiz/state/Initial';
import { useBodyScrollLock } from '~/hooks/useBodyScrollLock';

export default function LoadingSpinner() {
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const quizState: QuizState = useAppSelector((state) => state.quiz);
    const userState: UserState = useAppSelector((state) => state.user);

    const isLoading =
        quizRoomState.isLoading ||
        quizRoomState.isLoadingHighscores ||
        quizState.isLoading ||
        userState.isLoading;
    useBodyScrollLock(isLoading);
    if (!isLoading) {
        return null;
    }

    return (
        <div className="loading-overlay" role="status" aria-live="polite">
            <div className="loading-content">
                <div className="loading-spinner" />
                <p className="loading-text">Loading...</p>
            </div>
        </div>
    );
}
