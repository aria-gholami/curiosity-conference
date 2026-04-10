import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import initialState, {type UserState} from "~/features/user/state/Initial";
import {registerUser} from "~/features/user/reducers/registerUser";
import {deleteUser} from "~/features/user/reducers/deleteUser";

const userSlice = createSlice({
    name: 'user',
    initialState,
    reducers: {
        reset(state: UserState): void {
            state.isLoading = false;
            state.userId = '';
            state.userName = '';
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(registerUser.pending, (state: UserState) => {
                state.isLoading = true;
            })
            .addCase(
                registerUser.fulfilled,
                (state: UserState, action: PayloadAction<UserState>) => {
                    state.userId = action.payload.userId;
                    state.userName = action.payload.userName;
                    state.isLoading = false;
                },
            )
            .addCase(registerUser.rejected, (state: UserState) => {
                state.isLoading = false;
                state.userId = '';
                state.userName = '';
            })
            .addCase(deleteUser.pending, (state: UserState) => {
                state.isLoading = true;
            })
            .addCase(
                deleteUser.fulfilled,
                (state: UserState) => {
                    state.userId = '';
                    state.userName = '';
                    state.isLoading = false;
                },
            )
            .addCase(deleteUser.rejected, (state: UserState) => {
                state.isLoading = false;
            });;
    },
});

export const { reset } = userSlice.actions;

export default userSlice;
