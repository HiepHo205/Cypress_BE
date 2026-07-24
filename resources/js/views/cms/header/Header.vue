<script setup>
import { ref, computed } from "vue";
import LogoSection from "./components/LogoSection.vue";
import FaviconSection from "./components/FaviconSection.vue";
import MenuSection from "./components/MenuSection.vue";
import CountdownSection from "./components/CountdownSection.vue";
const activeTab = ref(1);
const tabs = [
    {
        id: 1,
        title: "Logo",
        description: "Website logo",
    },
    {
        id: 2,
        title: "Favicon",
        description: "Browser icon",
    },
    {
        id: 3,
        title: "Header Menu",
        description: "Navigation menu",
    },
    {
        id: 4,
        title: "Countdown",
        description: "Countdown & Button",
    },
];
const components = {
    1: LogoSection,
    2: FaviconSection,
    3: MenuSection,
    4: CountdownSection,
};
const currentComponent = computed(() => {
    return components[activeTab.value];
});
</script>
<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="mb-6 flex items-center justify-between rounded-2xl bg-white p-6 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Header Management
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    Configure all header settings, including logo, favicon,
                    navigation menu, and countdown.
                </p>
            </div>
        </div>
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-3">
                <div class="space-y-3 rounded-2xl bg-white p-4 shadow-sm">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
                        'flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left transition',
                        activeTab === tab.id
                            ? 'bg-blue-600 text-white'
                            : 'hover:bg-gray-100'
                    ]">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 font-semibold">
                            {{ tab.id }}
                        </div>
                        <div>
                            <div class="font-semibold">
                                {{ tab.title }}
                            </div>
                            <div class="text-xs" :class="activeTab === tab.id
                                ? 'text-blue-100'
                                : 'text-gray-400'
                                ">
                                {{ tab.description }}
                            </div>
                        </div>
                    </button>
                </div>
            </div>
            <div class="col-span-9 rounded-2xl bg-white p-6 shadow-sm">
                <KeepAlive>
                    <component :is="currentComponent" />
                </KeepAlive>
            </div>
        </div>
    </div>
</template>
