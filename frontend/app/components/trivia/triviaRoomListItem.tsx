import { ArrowRightIcon, TrophyIcon, UserGroupIcon } from '@heroicons/react/24/solid';
import type QuizRoomJoinRequestBody from '~/features/quizRoom/state/QuizRoomJoinRequestBody';
import { joinQuizRoom } from '~/features/quizRoom/reducers/joinQuizRoom';
import type { QuizRoom, QuizRoomState } from '~/features/quizRoom/state/Initial';
import { useAppDispatch, useAppSelector } from '~/redux/hooks';
import type { UserState } from '~/features/user/state/Initial';
import { useNavigate } from 'react-router';

export default function TriviaRoomListItem(props: { room: QuizRoom; onShowParticipants: any }) {
    const navigate = useNavigate();
    const dispatch = useAppDispatch();
    const quizRoomState: QuizRoomState = useAppSelector((state) => state.quizRoom);
    const userState: UserState = useAppSelector((state) => state.user);
    const closedRoomsByScoreGroup = [...quizRoomState.rooms]
        .filter((room) => room.status === 'closed')
        .sort((a, b) => (b.score ?? 0) - (a.score ?? 0));
    const closedIndex =
        props.room.status === 'closed'
            ? closedRoomsByScoreGroup.findIndex(
                  (quizRoom: QuizRoom) => quizRoom.id === props.room.id,
              )
            : -1;

    const isFirst = closedIndex === 0;
    const isSecond = closedIndex === 1;
    const isThird = closedIndex === 2;
    const isOtherClosed = closedIndex >= 3;

    return (
        <div
            className={`card flex flex-col gap-4 rounded-3xl px-6 py-5 sm:flex-row sm:items-center sm:justify-between transition-all ${
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
            <div className="flex items-start gap-4">
                <div
                    className={`icon-box relative flex h-12 w-12 items-center justify-center border ${
                        isFirst
                            ? 'border-amber-300 bg-amber-100 text-amber-600'
                            : isSecond
                              ? 'border-slate-300 bg-slate-200 text-slate-600'
                              : isThird
                                ? 'border-amber-700/30 bg-amber-100/70 text-amber-800'
                                : isOtherClosed
                                  ? 'border-slate-200 bg-slate-100 text-slate-600 font-bold text-sm'
                                  : 'border-slate-200 bg-slate-100 text-cyan-700'
                    }`}
                >
                    {isFirst ? (
                        <TrophyIcon className="h-6 w-6 text-amber-500 animate-pulse" />
                    ) : isSecond ? (
                        <TrophyIcon className="h-6 w-6 text-slate-400" />
                    ) : isThird ? (
                        <TrophyIcon className="h-6 w-6 text-amber-700" />
                    ) : isOtherClosed ? (
                        <span>#{closedIndex + 1}</span>
                    ) : (
                        <UserGroupIcon className="h-5 w-5" />
                    )}
                </div>

                <div>
                    <div className="flex items-center gap-2">
                        <h2 className="text-lg font-semibold text-dark-blue">{props.room.title}</h2>
                        {isFirst && (
                            <span className="inline-flex items-center gap-1 rounded-full bg-amber-400 px-2.5 py-0.5 text-xs font-bold text-white shadow-sm">
                                <span>1st Place</span>
                                <span>🏆</span>
                            </span>
                        )}
                        {isSecond && (
                            <span className="inline-flex items-center gap-1 rounded-full bg-slate-400 px-2.5 py-0.5 text-xs font-bold text-white shadow-sm">
                                <span>2nd Place</span>
                                <span>🥈</span>
                            </span>
                        )}
                        {isThird && (
                            <span className="inline-flex items-center gap-1 rounded-full bg-amber-700 px-2.5 py-0.5 text-xs font-bold text-white shadow-sm">
                                <span>3rd Place</span>
                                <span>🥉</span>
                            </span>
                        )}
                        {isOtherClosed && (
                            <span className="inline-flex items-center gap-1 rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                <span>Rank #{closedIndex + 1}</span>
                            </span>
                        )}
                    </div>
                    <p className="text-card-text mt-1 text-sm">
                        {props.room.participants.length} players · Status: {props.room.status}
                        {props.room.score !== undefined && props.room.status === 'closed'
                            ? ` · Highscore: ${props.room.score} pts`
                            : ''}
                    </p>
                </div>
            </div>
            <div className="flex flex-wrap items-center gap-3 self-start sm:self-auto">
                <button
                    type="button"
                    className="btn btn-secondary-light btn-md flex items-center gap-2"
                    onClick={() => props.onShowParticipants(props.room)}
                >
                    <UserGroupIcon className="h-4 w-4" />
                    <span>Participants</span>
                </button>
                {props.room.status === 'created' ? (
                    <button
                        className="btn btn-primary btn-md flex items-center gap-2"
                        onClick={() => {
                            const requestBody: QuizRoomJoinRequestBody = {
                                userId: userState.userId,
                                quizRoomId: props.room.id,
                            };
                            dispatch(joinQuizRoom(requestBody) as any);
                            navigate('/trivia/lobby');
                        }}
                    >
                        <span>Join</span>
                        <ArrowRightIcon className="h-4 w-4" />
                    </button>
                ) : null}
            </div>
        </div>
    );
}
