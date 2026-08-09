<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import LoadingOverlay from "@/components/common/LoadingOverlay.vue";


import {
    HomepageBanner,
    HomepageBusinessGrowth,
    HomepageIntroduction,
    HomepageWhyChooseCypress,
    HomepageCaseStudy,
    HomepagePricing,
    HomepageSuccessStories,
    // SectionGeneralInformation,
    HomepageOffer,
    HomepageContact,
    HomepageNews
} from './components';

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const isAuthError = ref(false);

const tabs = [
    {
        id: 'banner',
        title: 'Banner',
        description: 'Homepage banner',
        component: HomepageBanner
    },
    {
        id: 'business',
        title: 'Business Growth Model',
        description: 'Manage the business growth model section.',
        component: HomepageBusinessGrowth
    },
    {
        id: 'introduction',
        title: 'Introduction',
        description: 'Manage the introduction section.',
        component: HomepageIntroduction
    },
    {
        id: 'why-cypress',
        title: 'Why Choose CypressHub',
        description: 'Manage why customers choose Cypress section.',
        component: HomepageWhyChooseCypress
    },
    {
        id: 'case-study',
        title: 'Case Study',
        description: 'Manage case study section.',
        component: HomepageCaseStudy
    },
    {
        id: 'pricing',
        title: 'Pricing',
        description: 'Manage pricing section.',
        component: HomepagePricing
    },
    {
        id: 'success-stories',
        title: 'Success Stories',
        description: 'Manage success stories list.',
        component: HomepageSuccessStories
    },
    // {
    //     id: 'general-information',
    //     title: 'General Information',
    //     description: 'Reusable section information.',
    //     component: SectionGeneralInformation
    // },
    {
        id: 'offer',
        title: 'Launch Offer',
        description: 'Manage the limited-time launch offer.',
        component: HomepageOffer
    },
    {
        id: 'contact',
        title: 'Contact',
        description: 'Manage contact form.',
        component: HomepageContact
    },
    {
        id: 'news',
        title: 'News Today',
        description: 'Manage homepage news section.',
        component: HomepageNews
    }
];

const activeTab = computed(() => {
    return String(route.query.tab || 'banner');
});

const currentComponent = computed(() => {
    return (
        tabs.find((tab) => tab.id === activeTab.value)?.component ||
        HomepageBanner
    );
});

const handleLoading = (value: boolean) => {
    loading.value = value;
};

const handleAuthError = () => {
    loading.value = false;
    isAuthError.value = true;
};

const changeTab = async (id: string) => {
    if (id === activeTab.value) {
        return;
    }

    isAuthError.value = false;

    try {
        await router.push({
            query: {
                ...route.query,
                tab: id
            }
        });
    } catch (error) {
        console.error(error);
    }
};
onMounted(async () => {
    if (!route.query.tab) {
        await router.replace({
            query: {
                ...route.query,
                tab: 'banner'
            }
        });
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <!-- Header -->
        <div class="mb-6 rounded-2xl bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-900">
                Homepage Management
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Configure all homepage settings.
            </p>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <!-- Tabs -->
            <div class="col-span-3">
                <div class="space-y-3 rounded-2xl bg-white p-4 shadow-sm">
                    <button
                        v-for="(tab, index) in tabs"
                        :key="tab.id"
                        type="button"
                        @click="changeTab(tab.id)"
                        :class="[
                            'flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left transition',
                            activeTab === tab.id
                                ? 'bg-blue-600 text-white'
                                : 'hover:bg-gray-100'
                        ]"
                    >
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/20 font-semibold"
                        >
                            {{ index + 1 }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="truncate font-semibold">
                                {{ tab.title }}
                            </div>

                            <div
                                class="truncate text-xs"
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

            <!-- Content -->
            <div
                class="relative col-span-9 rounded-2xl bg-white p-6 shadow-sm"
            >
                <LoadingOverlay
                    :show="loading"
                    :fullScreen="false"
                    message="Loading..."
                />

                <!-- Auth error -->
                <div
                    v-if="isAuthError"
                    class="flex h-64 items-center justify-center"
                >
                    <p class="text-lg font-semibold text-red-500">
                        Session expired. Please login again.
                    </p>
                </div>

                <!-- Current tab -->
                <KeepAlive v-else>
                    <component
                        :is="currentComponent"
                        :key="activeTab"
                        @loading="handleLoading"
                        @auth-error="handleAuthError"
                    />
                </KeepAlive>
            </div>
        </div>
    </div>
</template>