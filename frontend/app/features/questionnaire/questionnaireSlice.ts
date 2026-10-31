import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, { type QuestionnaireState } from './state/Initial';
import { fetchQuestionnaireQuestions } from '~/features/questionnaire/reducers/fetchQuestionnaireQuestions';

const questionnaireSlice = createSlice({
    name: 'questionnaire',
    initialState,
    reducers: {
        reset(state: QuestionnaireState): void {
            state.questions = [];
            state.isLoading = false;
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(fetchQuestionnaireQuestions.pending, (state: QuestionnaireState) => {
                state.isLoading = true;
            })
            .addCase(
                fetchQuestionnaireQuestions.fulfilled,
                (state: QuestionnaireState, action: PayloadAction<QuestionnaireState>) => {
                    state.questions = action.payload.questions;
                    state.isLoading = false;
                },
            )
            .addCase(fetchQuestionnaireQuestions.rejected, (state: QuestionnaireState) => {
                state.isLoading = false;
            });
    },
});

export const { reset } = questionnaireSlice.actions;

export default questionnaireSlice;
