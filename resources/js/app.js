import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import '../css/app.css';

import { ApolloClient, InMemoryCache } from '@apollo/client/core';
import { DefaultApolloClient } from '@vue/apollo-composable';

import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const app = createApp(App);

const apolloClient = new ApolloClient({
    uri: '/graphql',
    cache: new InMemoryCache()
});

app.provide(DefaultApolloClient, apolloClient);

app.use(router);

app.use(Toast, {
    position: 'top-right',
    timeout: 3000,
    closeOnClick: true,
    pauseOnHover: true,
});

app.mount('#app');