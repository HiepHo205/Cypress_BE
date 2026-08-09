<template>
    <div class="rounded-3xl bg-white p-8 shadow-sm">
        <div>
            <h2 class="text-3xl font-semibold text-gray-900">
                News Management
            </h2>

            <p class="mt-2 text-gray-500">Manage homepage news section</p>
        </div>

        <div class="mt-6 flex gap-6 border-b border-gray-200">
            <button
                @click="activeTab = 'main'"
                :class="tabClass(activeTab === 'main')"
            >
                Main News
            </button>

            <button
                @click="activeTab = 'side'"
                :class="tabClass(activeTab === 'side')"
            >
                Side News
            </button>

            <button
                @click="activeTab = 'section-management'"
                :class="tabClass(activeTab === 'section-management')"
            >
                Sections Trends
            </button>
        </div>

        <div class="mt-6">
            <MainNews v-if="activeTab === 'main'" />

            <SideNews v-if="activeTab === 'side'" />

            <SectionManager
                v-if="activeTab === 'section-management'"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
const activeTab = ref('main');
import  MainNews  from './MainNews.vue';
import  SideNews  from './SideNews.vue';
import  SectionManager  from './SectionManager.vue';
const tabClass = (active: boolean) => {
    return active
        ? 'pb-3 text-blue-600 border-b-2 border-blue-600 font-semibold'
        : 'pb-3 text-gray-500 hover:text-blue-600';
};
</script>
