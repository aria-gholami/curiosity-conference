interface HighscoreByQuizRoomItem {
    userName: string;
    score: number;
}

export interface HighscoreByQuizRoomResponse {
    data: HighscoreByQuizRoomItem[];
}
