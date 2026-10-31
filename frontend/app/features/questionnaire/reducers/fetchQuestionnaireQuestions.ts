import { createAsyncThunk } from '@reduxjs/toolkit';
import type { RootState } from '~/redux/store';
import type {QuestionnaireState, Questions} from '~/features/questionnaire/state/Initial';
import type QuestionnaireQuestionsResponse from '~/features/questionnaire/state/QuestionnaireQuestionsResponse';

export const fetchQuestionnaireQuestions = createAsyncThunk<
    QuestionnaireState,
    void,
    { state: RootState }
>(
    'questionnaireQuestions/fetch',
    async () => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/questionnaire/questions`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
            });
            if (!response.ok) {
                throw new Error('Response by fetch questionnaire questions api is invalid.');
            }

            const responseBody: QuestionnaireQuestionsResponse = await response.json();
            const questionsMap = new Map<string, Questions>();
            responseBody.data.forEach(({ question, answer }) => {
                if (!questionsMap.has(question.id)) {
                    questionsMap.set(question.id, {
                        id: question.id,
                        title: question.title,
                        answers: [],
                    });
                }

                questionsMap.get(question.id)!.answers.push({
                    id: answer.id,
                    title: answer.title,
                });
            });
            const state: QuestionnaireState = {
                questions: Array.from(questionsMap.values()),
                isLoading: false,
            };

            return state;
        } catch (error) {
            console.error(error);

            throw error;
        }
    },
    {
        condition: (_, { getState }) => {
            const { questionnaire } = getState() as RootState;
            if (questionnaire.questions.length !== 0 && !questionnaire.isLoading) {
                return false;
            }
        },
    },
);
