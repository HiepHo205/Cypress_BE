<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import LogoSection from "./components/LogoSection.vue";
import FaviconSection from "./components/FaviconSection.vue";
import MenuSection from "./components/MenuSection.vue";
import CountdownSection from "./components/CountdownSection.vue";
import LoadingOverlay from "@/components/common/LoadingOverlay.vue";
const route = useRoute();
const router = useRouter();
const loading = ref(false);
const isAuthError = ref(false);
const tabs = [
    {
        id: "logo",
        title: "Logo",
        description: "Website logo"
    },
    {
        id: "favicon",
        title: "Favicon",
        description: "Browser icon"
    },
    {
        id: "menu",
        title: "Header Menu",
        description: "Navigation menu"
    },
    {
        id: "countdown",
        title: "Countdown",
        description: "Countdown & Button"
    }
];
const components = {
    logo: LogoSection,
    favicon: FaviconSection,
    menu: MenuSection,
    countdown: CountdownSection
};
const activeTab = computed(() => {
    return route.query.tab || "logo";
});
const currentComponent = computed(() => {
    return components[activeTab.value] || LogoSection;
});
onMounted(() => {
    if (!route.query.tab) {
        router.replace({
            path: "/admin/header",
            query: {
                tab: "logo"
            }
        });
    }
});
const handleLoading = (value) => {
    loading.value = value;
};
const handleAuthError = () => {
    isAuthError.value = true;
};
const changeTab = (id) => {
    router.push({
        path: "/admin/header",
        query: {
            tab: id
        }
    });
};
</script>
<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="mb-6 rounded-2xl bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-900">
                Header Management
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Configure all header settings, including logo, favicon,
                navigation menu, and countdown.
            </p>
        </div>
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-3">
                <div class="space-y-3 rounded-2xl bg-white p-4 shadow-sm">
                    <button v-for="(tab, index) in tabs" :key="tab.id" @click="changeTab(tab.id)" :class="[
                        'flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left transition',
                        activeTab === tab.id
                            ? 'bg-blue-600 text-white'
                            : 'hover:bg-gray-100'
                    ]">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 font-semibold">
                            {{ index + 1 }}
                        </div>
                        <div>
                            <div class="font-semibold">
                                {{ tab.title }}
                            </div>

                            <div class="text-xs" :class="activeTab === tab.id ? 'text-blue-100' : 'text-gray-400'">
                                {{ tab.description }}
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            <div class="relative col-span-9 rounded-2xl bg-white p-6 shadow-sm">
                <LoadingOverlay :show="loading" :fullScreen="false" message="Loading..." />
                <div v-if="isAuthError" class="flex h-64 items-center justify-center">
                    <p class="text-lg font-semibold text-red-500">
                        Session expired. Please login again.
                    </p>
                </div>
                <KeepAlive v-else>
                    <component :is="currentComponent" :key="activeTab" @loading="handleLoading"
                        @auth-error="handleAuthError" />
                </KeepAlive>
            </div>
        </div>
    </div>
</template>