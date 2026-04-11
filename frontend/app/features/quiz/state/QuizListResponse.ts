interface Quiz {
    id: string;
    title: string;
    description: string;
    category: string;
}

export interface QuizListResponse {
    data: Quiz[];
}
