console.log("APP JS LOADED");
import { createApp } from 'vue';
import App from './app.vue';
import router from './router';
import '../css/app.css';
import { DefaultApolloClient } from '@vue/apollo-composable';
import { apolloClient } from './apollo';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const app = createApp(App);

app.provide(DefaultApolloClient, apolloClient);

app.use(router);

app.use(Toast, {
    position: 'top-right',
    timeout: 3000,
    closeOnClick: true,
    pauseOnHover: true
});

app.mount('#app');
