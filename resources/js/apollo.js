import {
    ApolloClient,
    createHttpLink,
    InMemoryCache
} from '@apollo/client/core';
import { setContext } from '@apollo/client/link/context';

const httpLink = createHttpLink({
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

export const apolloClient = new ApolloClient({
    link: authLink.concat(httpLink),
    cache: new InMemoryCache()
});
