import { createRouter, createWebHistory } from 'vue-router';

import Dashboard from '@/views/Dashboard.vue';
import UserList from '@/views/users/UserList.vue';
import UserCreate from '@/views/users/UserCreate.vue';
import UserEdit from '@/views/users/UserEdit.vue';

const routes = [
    {
        path: '/admin',
        name: 'dashboard',
        component: Dashboard,
    },
    {
        path: '/admin/users',
        name: 'users.list',
        component: UserList,
    },
    {
        path: '/admin/users/create',
        name: 'users.create',
        component: UserCreate,
    },
    {
        path: '/admin/users/:id/edit',
        name: 'users.edit',
        component: UserEdit,
        props: true,
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
