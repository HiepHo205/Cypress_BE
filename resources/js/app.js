import { createApp } from 'vue';
import AdminDashboard from './components/AdminDashboard.vue';
import Login from './components/Login.vue';
import '../css/app.css';
const app = createApp({});

app.component('admin-dashboard', AdminDashboard);
app.component('login', Login);

app.mount('#app');
