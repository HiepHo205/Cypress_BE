import { createRouter, createWebHistory } from 'vue-router';

import AdminLayout from '@/layouts/AdminLayout.vue';

import Dashboard from '@/views/Dashboard.vue';
import UserList from '@/views/users/UserList.vue';
import UserCreate from '@/views/users/UserCreate.vue';
import UserEdit from '@/views/users/UserEdit.vue';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/auth/Login.vue'),
    },

    {
        path: '/admin',
        component: AdminLayout,
        children: [
            {
                path: '',
                name: 'dashboard',
                component: Dashboard,
            },
            {
                path: 'users',
                name: 'users.list',
                component: UserList,
            },
            {
                path: 'users/create',
                name: 'users.create',
                component: UserCreate,
            },
            {
                path: 'users/:id/edit',
                name: 'users.edit',
                component: UserEdit,
                props: true,
            },
        ],
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});