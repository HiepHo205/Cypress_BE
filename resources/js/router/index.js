import { createRouter, createWebHistory } from 'vue-router';

import AdminLayout from '@/layouts/AdminLayout.vue';

import Dashboard from '@/views/Dashboard.vue';
import UserList from '@/views/users/UserList.vue';
import UserCreate from '@/views/users/UserCreate.vue';
import UserEdit from '@/views/users/UserEdit.vue';
import UserDetailView from '@/views/users/UserDetail.vue';

import Header from '@/views/cms/header/Header.vue';
import Footer from '@/views/cms/footer/Footer.vue';

import { useToast } from 'vue-toastification';
import PlanList from '@/views/plans/PlanList.vue';
import PlanDetail from '@/views/plans/PlanDetail.vue';

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
            // Dashboard
            {
                path: '',
                name: 'dashboard',
                component: Dashboard
            },

            // User management
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
                path: 'roles',
                name: 'role.management',
                component: () => import('@/views/roles/RoleManagement.vue')
            },

            // Header CMS
            // URL:
            // /admin/header?tab=logo
            // /admin/header?tab=favicon
            // /admin/header?tab=menu
            // /admin/header?tab=countdown
            {
                path: 'header',
                name: 'cms.header',
                component: Header
            },
            {
                path: 'plans',
                name: 'plans.list',
                component: () =>
                    import('@/views/plans/PlanList.vue')
            },
            {
                path: 'plans/:id',
                name: 'plans.detail',
                component: () =>
                    import('@/views/plans/PlanDetail.vue'),
                props: true
            },
            // Footer CMS
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

// Check authentication
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');

    const requiresAuth = to.matched.some((route) => route.meta.requiresAuth);

    if (requiresAuth && !token) {
        const toast = useToast();

        toast.warning('Please login to access this page.');

        return next('/login');
    }

    // Nếu đã login mà vào login thì quay về admin
    if (to.name === 'login' && token) {
        return next('/admin');
    }

    next();
});

export default router;
