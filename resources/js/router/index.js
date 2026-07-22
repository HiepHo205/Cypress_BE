import { createRouter, createWebHistory } from 'vue-router';

import AdminLayout from '@/layouts/AdminLayout.vue';

import Dashboard from '@/views/Dashboard.vue';
import UserList from '@/views/users/UserList.vue';
import UserCreate from '@/views/users/UserCreate.vue';
import UserEdit from '@/views/users/UserEdit.vue';
import { useToast } from 'vue-toastification';
const routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/auth/Login.vue')
    },

    {
        path: '/admin',
        component: AdminLayout,
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: Dashboard
            },
            {
                path: 'users',
                name: 'users.list',
                component: UserList
            },
            {
                path: 'users/create',
                name: 'users.create',
                component: UserCreate
            },
            {
                path: 'users/:id/edit',
                name: 'users.edit',
                component: UserEdit,
                props: true
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');

    const requiresAuth = to.matched.some((route) => route.meta.requiresAuth);

    const toast = useToast();

    if (requiresAuth && !token) {
        toast.warning('Please login to access this page.');

        return next('/login');
    }

    if (to.path === '/login' && token) {
        return next('/admin');
    }

    next();
});

export default router;
