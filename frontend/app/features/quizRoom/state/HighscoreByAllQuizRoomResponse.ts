interface HighscoreByQuizRoomItem {
    quizRoomTitle: string;
    quizRoomScore: number;
}

export interface HighscoreByAllQuizRoomResponse {
    data: HighscoreByQuizRoomItem[];
}
