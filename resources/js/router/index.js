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
import HomePage from '../views/home/HomePage.vue';
import NewsManagement from '../views/news/components/NewsManagement.vue';
import CaseStudyManagement from '../views/case-studies/components/CaseStudyManagement.vue';

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
            {
                path: 'header',
                name: 'cms.header',
                component: Header
            },
            {
                path: 'plans',
                name: 'plans.list',
                component: () => import('@/views/plans/PlanList.vue')
            },
            {
                path: 'plans/:id',
                name: 'plans.detail',
                component: () => import('@/views/plans/PlanDetail.vue'),
                props: true
            },
            {
                path: 'footer',
                name: 'cms.footer',
                component: Footer
            },
            {
                path: 'homepage',
                name: 'cms.homepage',
                component: HomePage
            },
            {
                path: 'news',
                name: 'news.management',
                component: NewsManagement
            },
            {
                path: 'case-studies',
                name: 'case-studies.management',
                component: CaseStudyManagement
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

    if (requiresAuth && !token) {
        const toast = useToast();

        toast.warning('Please login to access this page.');

        return next('/login');
    }
    if (to.name === 'login' && token) {
        return next('/admin');
    }

    next();
});

export default router;
