<template>
    <div class="w-64 h-screen bg-slate-900 text-white flex flex-col">
        <div class="p-6 border-b border-slate-800">
            <h1 class="text-2xl font-bold">
                Cypress
            </h1>
        </div>

        <nav class="flex-1 p-4">
            <ul class="space-y-2">
                <li>
                    <router-link to="/admin"
                        class="flex items-center px-4 py-3 rounded-lg transition hover:bg-slate-800"
                        exact-active-class="bg-slate-800">
                        Dashboard
                    </router-link>
                </li>

                <li>
                    <router-link to="/admin/users"
                        class="flex items-center px-4 py-3 rounded-lg transition hover:bg-slate-800"
                        exact-active-class="bg-slate-800">
                        Users
                    </router-link>
                </li>
            </ul>
        </nav>

        <div class="p-4 border-t border-slate-700">
            <button @click="showLogoutModal = true"
                class="flex items-center w-full gap-3 px-4 py-3 rounded-lg text-red-400 hover:bg-slate-800 hover:text-red-300 transition">
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

<script setup>
import { ref } from "vue";
import { LogOut } from "lucide-vue-next";
import { useLogout } from "@/composables/useLogout";

const showLogoutModal = ref(false);

const { logout } = useLogout();

async function confirmLogout() {
    showLogoutModal.value = false;
    await logout();
}
</script>