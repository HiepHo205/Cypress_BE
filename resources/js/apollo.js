import { ApolloClient, InMemoryCache } from '@apollo/client/core';
import { setContext } from '@apollo/client/link/context';
import { createUploadLink } from 'apollo-upload-client';
import { onError } from '@apollo/client/link/error';
import { useToast } from 'vue-toastification';

const uploadLink = createUploadLink({
    uri: import.meta.env.VITE_GRAPHQL_URL || '/graphql',
    credentials: 'include'
});

const authLink = setContext((_, { headers }) => {
    const token = localStorage.getItem('token');

    return {
        headers: {
            ...headers,
            ...(token && {
                Authorization: `Bearer ${token}`
            })
        }
    };
});

let isLoggingOut = false;

const errorLink = onError(({ graphQLErrors, networkError }) => {
    const toast = useToast();

    const handleLogout = () => {
        if (isLoggingOut) return;

        isLoggingOut = true;

        localStorage.removeItem('token');

        toast.warning('Your session has expired. Please log in again.');

        window.location.href = '/login';
    };

    if (graphQLErrors) {
        graphQLErrors.forEach((err) => {
            if (
                err.message === 'Unauthenticated.' ||
                err.message === 'Unauthenticated'
            ) {
                handleLogout();
            }
        });
    }

    if (networkError && networkError.statusCode === 401) {
        handleLogout();
    }
});

export const apolloClient = new ApolloClient({
    link: errorLink.concat(authLink.concat(uploadLink)),
    cache: new InMemoryCache()
});
