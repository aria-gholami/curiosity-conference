import { createAsyncThunk } from '@reduxjs/toolkit';
import type { RootState } from '~/redux/store';
import type { UserDeleteRequestBody } from '~/features/user/state/UserDeleteRequestBody';

export const deleteUser = createAsyncThunk<void, UserDeleteRequestBody, { state: RootState }>(
    'user/delete',
    async (userDeleteRequestBody: UserDeleteRequestBody) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/user?userId=${userDeleteRequestBody.userId}`;
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
            });
            if (!response.ok) {
                throw new Error('Response by delete user api is invalid.');
            }
        } catch (error) {
            console.error(error);

            throw error;
        }
    },
    {
        condition: (_arg, { getState }) => {
            const { user } = getState() as RootState;
            if (user.isLoading || user.userId === '') {
                return false;
            }
        },
    },
);
