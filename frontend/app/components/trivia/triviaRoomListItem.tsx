import { ArrowRightIcon, TrophyIcon, UserGroupIcon } from '@heroicons/react/24/solid';
import type QuizRoomJoinRequestBody from '~/features/quizRoom/state/QuizRoomJoinRequestBody';
import { joinQuizRoom } from '~/features/quizRoom/reducers/joinQuizRoom';
import type { QuizRoom, QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import type { UserState } from '~/features/user/state/Initial';
import { useNavigate } from 'react-router';

type TriviaRoomListItemProps = {
    room: QuizRoom;
    onShowParticipants: (room: QuizRoom) => void;
};

export default function TriviaRoomListItem({ room, onShowParticipants }: TriviaRoomListItemProps) {
    const navigate = useNavigate();
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);

    const finishedScores = [
        ...new Set(
            quizRoomState.rooms
                .filter((quizRoom) => quizRoom.status === 'finished')
                .map((quizRoom) => quizRoom.score ?? 0),
        ),
    ].sort((a, b) => b - a);

    const finishedIndex = room.status === 'finished' ? finishedScores.indexOf(room.score ?? 0) : -1;

    const isFirst = finishedIndex === 0;
    const isSecond = finishedIndex === 1;
    const isThird = finishedIndex === 2;
    const isOtherClosed = finishedIndex >= 3;

    return (
        <div
            className={`card flex w-full min-w-0 flex-col gap-4 rounded-3xl px-4 py-5 transition-all sm:px-6 lg:flex-row lg:items-center lg:justify-between ${
                isFirst
                    ? 'border-2 border-amber-300 bg-amber-50/80 shadow-md ring-2 ring-amber-300/40'
                    : isSecond
                      ? 'border-2 border-slate-300 bg-slate-100/80 shadow-sm ring-1 ring-slate-300/40'
                      : isThird
                        ? 'border-2 border-amber-700/30 bg-orange-50/60 shadow-sm ring-1 ring-amber-700/20'
                        : isOtherClosed
                          ? 'border border-slate-200 bg-slate-50/60'
                          : ''
            }`}
        >
            <div className="flex min-w-0 flex-1 items-start gap-3 sm:gap-4">
                <div
                    className={`icon-box relative flex h-12 w-12 shrink-0 items-center justify-center border ${
                        isFirst
                            ? 'border-amber-300 bg-amber-100 text-amber-600'
                            : isSecond
                              ? 'border-slate-300 bg-slate-200 text-slate-600'
                              : isThird
                                ? 'border-amber-700/30 bg-amber-100/70 text-amber-800'
                                : isOtherClosed
                                  ? 'border-slate-200 bg-slate-100 text-sm font-bold text-slate-600'
                                  : 'border-slate-200 bg-slate-100 text-cyan-700'
                    }`}
                >
                    {isFirst ? (
                        <TrophyIcon className="h-6 w-6 animate-pulse text-amber-500" />
                    ) : isSecond ? (
                        <TrophyIcon className="h-6 w-6 text-slate-400" />
                    ) : isThird ? (
                        <TrophyIcon className="h-6 w-6 text-amber-700" />
                    ) : isOtherClosed ? (
                        <span>#{finishedIndex + 1}</span>
                    ) : (
                        <UserGroupIcon className="h-5 w-5" />
                    )}
                </div>
                <div className="min-w-0 flex-1">
                    <div className="flex min-w-0 flex-wrap items-center gap-2">
                        <h2
                            className="min-w-0 max-w-full flex-1 basis-full truncate text-lg font-semibold text-dark-blue sm:basis-auto"
                            title={room.title}
                        >
                            {room.title}
                        </h2>
                        {isFirst && (
                            <span className="inline-flex shrink-0 items-center gap-1 whitespace-nowrap rounded-full bg-amber-400 px-2.5 py-1 text-xs font-bold text-white shadow-sm">
                                1st Place 🏆
                            </span>
                        )}
                        {isSecond && (
                            <span className="inline-flex shrink-0 items-center gap-1 whitespace-nowrap rounded-full bg-slate-400 px-2.5 py-1 text-xs font-bold text-white shadow-sm">
                                2nd Place 🥈
                            </span>
                        )}
                        {isThird && (
                            <span className="inline-flex shrink-0 items-center gap-1 whitespace-nowrap rounded-full bg-amber-700 px-2.5 py-1 text-xs font-bold text-white shadow-sm">
                                3rd Place 🥉
                            </span>
                        )}
                        {isOtherClosed && (
                            <span className="inline-flex shrink-0 items-center whitespace-nowrap rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                Rank #{finishedIndex + 1}
                            </span>
                        )}
                    </div>
                    <p className="text-card-text mt-2 break-words text-sm leading-5">
                        {room.participants.length} players · Status: {room.status}
                        {room.score !== undefined && room.status === 'finished'
                            ? ` · Highscore: ${room.score} pts`
                            : ''}
                    </p>
                </div>
            </div>
            <div className="flex w-full min-w-0 flex-col gap-3 sm:flex-row lg:w-auto lg:shrink-0">
                <button
                    type="button"
                    className="btn btn-secondary-light btn-md flex w-full select-none items-center justify-center gap-2 sm:flex-1 lg:w-auto lg:flex-none"
                    onClick={() => onShowParticipants(room)}
                >
                    <UserGroupIcon className="h-4 w-4 shrink-0" />
                    <span>Participants</span>
                </button>
                {room.status === 'created' && (
                    <button
                        type="button"
                        className="btn btn-primary btn-md flex w-full select-none items-center justify-center gap-2 sm:flex-1 lg:w-auto lg:flex-none"
                        onClick={() => {
                            const requestBody: QuizRoomJoinRequestBody = {
                                userId: userState.userId,
                                quizRoomId: room.id,
                            };
                            dispatch(joinQuizRoom(requestBody) as any);
                            navigate('/trivia/lobby');
                        }}
                    >
                        <span>Join</span>
                        <ArrowRightIcon className="h-4 w-4 shrink-0" />
                    </button>
                )}
            </div>
        </div>
    );
}
