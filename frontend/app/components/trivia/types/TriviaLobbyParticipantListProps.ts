import type { ConfirmationAction } from '~/components/trivia/types/ConfirmationAction';

export default interface TriviaLobbyParticipantListProps {
    setConfirmation: (confirmationAction: ConfirmationAction) => void;
}
