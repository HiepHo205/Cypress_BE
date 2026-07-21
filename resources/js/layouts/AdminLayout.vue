<script setup>
import Sidebar from '@/components/Sidebar.vue';
</script>

<template>
    <div class="flex min-h-screen bg-slate-100">

        <Sidebar />

        <div class="flex-1 flex flex-col">

            <header
                class="h-16 bg-white border-b border-slate-200 flex items-center px-6"
            >
                <h2 class="text-lg font-semibold">
                    Admin Portal
                </h2>
            </header>

            <main class="flex-1 p-6">
                <router-view />
            </main>

        </div>

    </div>
</template>