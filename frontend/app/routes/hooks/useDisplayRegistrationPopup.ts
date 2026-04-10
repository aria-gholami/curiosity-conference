import {useEffect} from "react";
import type {UserState} from "~/features/user/state/Initial";
import {useAppSelector} from "~/redux/hooks";

export const useDisplayRegistrationPopup = (
    setIsPopupOpen: (shouldDisplayPopup: boolean) => void) => {
    const userState: UserState = useAppSelector(
        (state) => state.user,
    );

    useEffect(() => {
        if (userState.userId === '') {
            setIsPopupOpen(true);
        }
        if (userState.userId !== '') {
            setIsPopupOpen(false);
        }
    }, [userState]);
};
