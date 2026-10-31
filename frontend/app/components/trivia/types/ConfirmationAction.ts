export type ConfirmationAction =
    | {
          type: 'remove';
          participantId: string;
          participantName: string;
      }
    | { type: 'delete' }
    | { type: 'leave' }
    | { type: 'start' }
    | { type: 'next' };
