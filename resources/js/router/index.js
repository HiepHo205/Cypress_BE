import { createRouter, createWebHistory } from 'vue-router';

import AdminLayout from '@/layouts/AdminLayout.vue';

import Dashboard from '@/views/Dashboard.vue';
import UserList from '@/views/users/UserList.vue';
import UserCreate from '@/views/users/UserCreate.vue';
import UserEdit from '@/views/users/UserEdit.vue';
import UserDetailView from '@/views/users/UserDetail.vue';
import { useToast } from 'vue-toastification';
import Header from '../views/cms/header/Header.vue';
import Footer from '../views/cms/footer/Footer.vue';
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
                path: 'roles',
                name: 'role.management',
                component: () => import('@/views/roles/RoleManagement.vue')

            },
            {
                path: 'users/:id',
                name: 'users.detail',
                component: UserDetailView,
                props: true
            },
                        {
                path: 'users/:id/edit',
                name: 'users.edit',
                component: UserEdit,
                props: true
            },
            {
                path: 'header',
                name: 'cms.header',
                component: Header
            },
            {
                path: 'footer',
                name: 'cms.footer',
                component: Footer
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
