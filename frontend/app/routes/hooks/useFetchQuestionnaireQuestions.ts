import { useAppDispatch } from '~/redux/hooks';
import { useEffect } from 'react';
import { fetchQuestionnaireQuestions } from '~/features/questionnaire/reducers/fetchQuestionnaireQuestions';

export default function useFetchQuestionnaireQuestions(): void {
    const dispatch = useAppDispatch();

    useEffect(() => {
        dispatch(fetchQuestionnaireQuestions());
    });
}
