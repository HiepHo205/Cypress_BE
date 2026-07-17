import { createRouter, createWebHistory } from 'vue-router'

import Dashboard from '@/views/Dashboard.vue'
import UserList from '@/views/users/UserList.vue'

const routes = [
    {
        path: '/admin',
        name: 'dashboard',
        component: Dashboard
    },
    {
        path: '/admin/users',
        name: 'users.list',
        component: UserList
    }
]

export default createRouter({
    history: createWebHistory(),
    routes
})