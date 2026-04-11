import { createAsyncThunk } from '@reduxjs/toolkit';
import type { UserState } from '~/features/user/state/Initial';
import type { RegistrationRequestBody } from '~/features/user/state/RegistrationRequestBody';
import type { RootState } from '~/redux/store';
import type RegistrationResponse from '~/features/user/state/RegistrationResponse';

export const registerUser = createAsyncThunk<
    UserState,
    RegistrationRequestBody,
    { state: RootState }
>(
    'user/register',
    async (registrationRequestBody: RegistrationRequestBody) => {
        try {
            const url: string = `${import.meta.env.VITE_BACKEND_API_BASE_URL}/user`;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'api-key': import.meta.env.VITE_BACKEND_API_KEY ?? '',
                },
                body: JSON.stringify(registrationRequestBody),
            });
            if (!response.ok) {
                throw new Error('Response by regster user api is invalid.');
            }

            const responseBody: RegistrationResponse = await response.json();
            const registrationState: UserState = {
                userId: responseBody.id,
                userName: responseBody.userName,
                isLoading: true,
            };

            return registrationState;
        } catch (error) {
            console.error(error);

            throw error;
        }
    },
    {
        condition: (_arg, { getState }) => {
            const { user } = getState() as RootState;
            if (user.isLoading || user.userId !== '') {
                return false;
            }
        },
    },
);
