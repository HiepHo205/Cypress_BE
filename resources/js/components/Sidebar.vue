<script setup>
import { ref, watch } from "vue";
import { useRoute } from "vue-router";
import {
    LogOut,
    LayoutDashboard,
    Users,
    ShieldCheck,
    FolderTree,
    PanelTop,
    PanelBottom,
    CreditCard,
    Newspaper,
    ChevronDown,
    ChevronRight
} from "lucide-vue-next";
import { useLogout } from "@/composables/useLogout";
const route = useRoute();
const showLogoutModal = ref(false);
const openStructure = ref(false);
const structureRoutes = [
    "/admin/header",
    "/admin/footer"
];
const openPage = ref(false);
const pageRoutes = [
    "/admin/homepage"
];
watch(
    () => route.path,
    (path) => {
        openStructure.value = structureRoutes.some(routeItem =>
            path.startsWith(routeItem)
        );

        openPage.value = pageRoutes.some(routeItem =>
            path.startsWith(routeItem)
        );
    },
    {
        immediate: true
    }
);
const { logout } = useLogout();
async function confirmLogout() {
    showLogoutModal.value = false;
    await logout();
}
</script>
<template>
    <div class="w-64 h-screen bg-slate-900 text-white flex flex-col">
        <div class="p-6 border-b border-slate-800">
            <h1 class="text-2xl font-bold">Cypress</h1>
        </div>

        <nav class="flex-1 p-4">
            <ul class="space-y-2">
                <li>
                    <router-link to="/admin"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition hover:bg-slate-800"
                        exact-active-class="bg-slate-800">
                        <LayoutDashboard :size="18" />
                        <span>Dashboard</span>
                    </router-link>
                </li>
                <li>
                    <router-link to="/admin/users"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition hover:bg-slate-800"
                        exact-active-class="bg-slate-800">
                        <Users :size="18" />
                        <span>Users</span>
                    </router-link>
                </li>
                <li>
                    <router-link to="/admin/roles"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition hover:bg-slate-800"
                        exact-active-class="bg-slate-800">
                        <ShieldCheck :size="18" />
                        <span>Roles</span>
                    </router-link>
                </li>
                <li>
                    <router-link
                        to="/admin/plans"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition hover:bg-slate-800"
                        exact-active-class="bg-slate-800"
                    >
                        <CreditCard :size="18" />
                        <span>Subscriptions</span>
                    </router-link>
                </li>

                <li>
                    <button @click="openStructure = !openStructure"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition hover:bg-slate-800">
                        <div class="flex items-center gap-3">
                            <FolderTree :size="18" />
                            <span>General Structure</span>
                        </div>

                        <ChevronDown v-if="openStructure" :size="18" />

                        <ChevronRight v-else :size="18" />
                    </button>
                    <transition enter-active-class="transition-all duration-300 ease-out"
                        leave-active-class="transition-all duration-300 ease-in">
                        <ul v-show="openStructure" class="mt-2 ml-6 space-y-1">
                            <li>
                                <router-link to="/admin/header"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition hover:bg-slate-800"
                                    active-class="bg-slate-800">
                                    <PanelTop :size="16" />
                                    <span>Header</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/admin/footer"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition hover:bg-slate-800"
                                    active-class="bg-slate-800">
                                    <PanelBottom :size="16" />
                                    <span>Footer</span>
                                </router-link>
                            </li>
                        </ul>
                    </transition>
                </li>
                <li>
                    <router-link to="/admin/homepage"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition hover:bg-slate-800"
                        exact-active-class="bg-slate-800">
                        <FolderTree :size="18" />
                        <span>Page</span>
                    </router-link>
                </li>
                <li>
                    <router-link to="/admin/news"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition hover:bg-slate-800"
                        exact-active-class="bg-slate-800">
                        <Newspaper :size="18" />
                        <span>News</span>
                    </router-link>
                </li>
            </ul>
        </nav>
        <div class="p-4 border-t border-slate-700">
            <button @click="showLogoutModal = true"
                class="flex items-center w-full gap-3 px-4 py-3 rounded-lg text-white transition hover:bg-slate-800">
                <LogOut :size="18" />
                <span>Logout</span>
            </button>
        </div>
    </div>
    <div v-if="showLogoutModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50">
        <div class="w-[400px] rounded-xl bg-white p-6 shadow-xl">
            <h2 class="text-xl font-semibold text-gray-800">
                Logout
            </h2>
            <p class="mt-3 text-gray-600">
                Are you sure you want to log out?
            </p>
            <div class="mt-6 flex justify-end gap-3">

                <button @click="showLogoutModal = false"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-100">
                    Cancel
                </button>
                <button @click="confirmLogout" class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                    Logout
                </button>
            </div>
        </div>
    </div>
</template>
