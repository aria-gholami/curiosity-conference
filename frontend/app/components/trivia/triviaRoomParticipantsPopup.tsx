import { UserGroupIcon, XMarkIcon } from '@heroicons/react/24/solid';
import type { QuizRoom } from '~/features/quizRoom/state/Initial';
import { useAppSelector } from '~/redux/hooks';
import { useBodyScrollLock } from '~/hooks/useBodyScrollLock';

interface TriviaRoomParticipantsPopupProps {
    room: QuizRoom;
    open: boolean;
    onClose: () => void;
}

export default function TriviaRoomParticipantsPopup({
    room,
    open,
    onClose,
}: TriviaRoomParticipantsPopupProps) {
    const currentRoom = useAppSelector((state) =>
        state.quizRoom.rooms.find((item) => item.id === room.id),
    );
    const displayRoom = currentRoom ?? room;
    if (!open) {
        return null;
    }
    useBodyScrollLock(open);

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div className="absolute inset-0 bg-dark-blue/55 backdrop-blur-md" onClick={onClose} />

            <div className="glass relative w-full max-w-lg overflow-hidden rounded-[2rem] border border-white/20 bg-white/90">
                <div className="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-turquoise/15 blur-3xl" />
                <div className="pointer-events-none absolute -left-12 bottom-0 h-32 w-32 rounded-full bg-dark-blue/20 blur-3xl" />
                <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,0.6)_0%,transparent_55%)]" />

                <div className="relative p-6 sm:p-8">
                    <div className="flex items-start justify-between gap-4">
                        <div>
                            <div className="inline-flex items-center gap-2 rounded-full border border-turquoise/20 bg-turquoise/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-dark-blue">
                                <UserGroupIcon className="h-3.5 w-3.5" />
                                <span>Participants</span>
                            </div>

                            <h2 className="mt-4 text-2xl font-semibold tracking-[-0.02em] text-dark-blue sm:text-3xl">
                                {room.title}
                            </h2>

                            <p className="mt-2 text-sm leading-6 text-card-text sm:text-base">
                                All participants in this room ({room.participants.length})
                            </p>
                        </div>

                        <button
                            type="button"
                            onClick={onClose}
                            className="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer"
                        >
                            <XMarkIcon className="h-6 w-6" />
                        </button>
                    </div>

                    <div className="mt-6 flex max-h-60 min-w-0 flex-col gap-3 overflow-y-auto overflow-x-hidden pr-1">
                        {displayRoom.participants.length === 0 ? (
                            <div className="py-8 text-center text-sm text-card-text">
                                No participants in this room yet.
                            </div>
                        ) : (
                            displayRoom.participants.map((participant) => {
                                const isHost = participant.userId === displayRoom.host.id;

                                return (
                                    <div
                                        key={participant.userId}
                                        className="flex w-full min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3"
                                    >
                                        <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-dark-blue text-xs font-semibold text-white">
                                            {participant.userName.slice(0, 1).toUpperCase()}
                                        </div>

                                        <div className="flex min-w-0 flex-1 flex-col">
                                            <p
                                                className="truncate text-sm font-semibold text-dark-blue"
                                                title={participant.userName}
                                            >
                                                {participant.userName}
                                            </p>

                                            <p className="text-xs text-card-text">
                                                {isHost ? 'Host' : 'Player'}
                                            </p>
                                        </div>
                                    </div>
                                );
                            })
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
