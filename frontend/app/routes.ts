import { type RouteConfig, index, route } from '@react-router/dev/routes';

export default [
    index('routes/home.tsx'),
    route('/trivia/list', './routes/triviaRoomList.tsx'),
    route('/trivia/lobby', './routes/triviaLobby.tsx'),
] satisfies RouteConfig;
