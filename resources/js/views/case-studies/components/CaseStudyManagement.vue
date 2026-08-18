<script setup>
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import CaseStudyBanner from './CaseStudyBanner.vue';
import CaseStudyCategories from './CaseStudyCategories.vue';
import CaseStudies from './CaseStudies.vue';

import { useCaseStudy } from '../../../composables/caseStudy/useCaseStudy';

defineOptions({
    name: 'CaseStudyManagement'
});

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const isAuthError = ref(false);

const {
    banner,
    categories,
    caseStudies
} = useCaseStudy();

const tabs = [
    {
        id: 'banner',
        title: 'Case Study Banner',
        description: 'Manage page heading and description.',
        component: CaseStudyBanner
    },
    {
        id: 'categories',
        title: 'Categories',
        description: 'Manage case study categories.',
        component: CaseStudyCategories
    },
    {
        id: 'case-studies',
        title: 'Case Studies',
        description: 'Manage case study projects.',
        component: CaseStudies
    }
];

const activeTab = computed(() => {
    const tab = String(route.query.tab || 'banner');

    return tabs.some((item) => item.id === tab)
        ? tab
        : 'banner';
});

const currentTab = computed(() => {
    return (
        tabs.find((tab) => tab.id === activeTab.value) ||
        tabs[0]
    );
});

const currentComponent = computed(() => {
    return currentTab.value.component;
});

const currentProps = computed(() => {
    switch (activeTab.value) {
        case 'banner':
            return {
                data: banner.value
            };

        case 'categories':
            return {
                items: categories.value
            };

        case 'case-studies':
            return {
                items: caseStudies.value,
                categories: categories.value
            };

        default:
            return {};
    }
});

const changeTab = async (id) => {
    if (id === activeTab.value) {
        return;
    }

    loading.value = false;
    isAuthError.value = false;

    await router.push({
        query: {
            ...route.query,
            tab: id
        }
    });
};

const handleLoading = (value) => {
    loading.value = value;
};

const handleAuthError = () => {
    loading.value = false;
    isAuthError.value = true;
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="mb-6 rounded-2xl bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-900">
                Case Study Management
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Manage banner, categories and case studies displayed on the
                public Case Study page.
            </p>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-3">
                <div class="rounded-2xl bg-white p-4 shadow-sm">
                    <div class="mb-4 px-2">
                        <h2
                            class="text-xs font-semibold uppercase tracking-wider text-gray-400"
                        >
                            Case Study Sections
                        </h2>
                    </div>

                    <div class="space-y-2">
                        <button
                            v-for="(tab, index) in tabs"
                            :key="tab.id"
                            type="button"
                            class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left transition"
                            :class="
                                activeTab === tab.id
                                    ? 'bg-blue-600 text-white shadow-sm'
                                    : 'text-gray-700 hover:bg-gray-100'
                            "
                            @click="changeTab(tab.id)"
                        >
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg font-semibold"
                                :class="
                                    activeTab === tab.id
                                        ? 'bg-white/20 text-white'
                                        : 'bg-gray-100 text-gray-500'
                                "
                            >
                                {{ index + 1 }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-semibold">
                                    {{ tab.title }}
                                </div>

                                <div
                                    class="mt-0.5 text-xs"
                                    :class="
                                        activeTab === tab.id
                                            ? 'text-blue-100'
                                            : 'text-gray-400'
                                    "
                                >
                                    {{ tab.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="relative col-span-9 min-h-[650px] rounded-2xl bg-white p-6 shadow-sm"
            >
                <div
                    v-if="loading"
                    class="absolute inset-0 z-20 flex items-center justify-center rounded-2xl bg-white/70 backdrop-blur-sm"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="h-5 w-5 animate-spin rounded-full border-2 border-gray-200 border-t-blue-600"
                        />

                        <span class="text-sm font-medium text-gray-600">
                            Loading...
                        </span>
                    </div>
                </div>

                <div
                    v-if="isAuthError"
                    class="flex h-64 items-center justify-center"
                >
                    <div class="text-center">
                        <p class="text-lg font-semibold text-red-500">
                            Session expired.
                        </p>

                        <p class="mt-1 text-sm text-gray-400">
                            Please login again.
                        </p>
                    </div>
                </div>

                <div v-else class="h-full">
                    <KeepAlive>
                        <component
                            :is="currentComponent"
                            v-bind="currentProps"
                            @loading="handleLoading"
                            @auth-error="handleAuthError"
                        />
                    </KeepAlive>
                </div>
            </div>
        </div>
    </div>
</template>