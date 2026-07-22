<script setup>
import Sidebar from "@/components/Sidebar.vue";
</script>

<template>
    <div class="flex h-screen bg-slate-100">
        <Sidebar />

        <div class="flex-1 flex flex-col overflow-hidden">

            <header
                class="h-16 bg-white border-b border-slate-200 flex items-center px-6 shrink-0"
            >
                <h2 class="text-lg font-semibold">
                    Admin Portal
                </h2>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <router-view />
            </main>

        </div>
    </div>
</template>