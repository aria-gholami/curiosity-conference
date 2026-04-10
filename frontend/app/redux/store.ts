import { configureStore, combineReducers } from '@reduxjs/toolkit';
import { persistReducer, persistStore } from 'redux-persist';
import storage from 'redux-persist/lib/storage';
import quizRoomCreateSlice from '~/features/quizRoomCreate/quizRoomCreateSlice';
import userSlice from "~/features/user/userSlice";

const createNoopStorage = () => ({
    getItem(_key: string) {
        return Promise.resolve(null);
    },
    setItem(_key: string, value: any) {
        return Promise.resolve(value);
    },
    removeItem(_key: string) {
        return Promise.resolve();
    },
});
const getStorage = () => {
    if (typeof window === 'undefined') {
        return createNoopStorage();
    }
    if (storage && typeof (storage as any).getItem === 'function') {
        return storage;
    }
    if (
        storage &&
        (storage as any).default &&
        typeof (storage as any).default.getItem === 'function'
    ) {
        return (storage as any).default;
    }
    return storage || createNoopStorage();
};
const storageEngine = getStorage();

const rootReducer = combineReducers({
    quizRoomCreate: quizRoomCreateSlice.reducer,
    user: userSlice.reducer,
});
const persistConfig = {
    key: 'root',
    storage: storageEngine,
    whitelist: ['user'],
};
const persistedReducer = persistReducer(persistConfig, rootReducer);
export const store = configureStore({
    reducer: persistedReducer,
    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware({
            serializableCheck: {
                ignoredActions: ['persist/PERSIST', 'persist/REHYDRATE'],
            },
        }),
});

export const persistor = persistStore(store);
export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;
