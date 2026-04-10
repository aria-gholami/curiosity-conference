export interface UserState {
    userId: string;
    userName: string;
    isLoading: boolean;
}

const initialState: UserState = {
    userId: '',
    userName: '',
    isLoading: false,
};

export default initialState;
