<script setup>
import { ref, computed } from 'vue';

import FooterBrandingSection from './components/FooterBrandingSection.vue';
import FooterSocialSection from './components/FooterSocialSection.vue';
import FooterNavigationSection from './components/FooterNavigationSection.vue';
import FooterNewsletterSection from './components/FooterNewsletterSection.vue';
import FooterBottomBarSection from './components/FooterBottomBarSection.vue';

import LoadingOverlay from '@/components/common/LoadingOverlay.vue';

const activeTab = ref(1);
const tabLoading = ref(false);
const contentLoading = ref(false);

const tabs = [
    {
        id: 1,
        title: 'Branding',
        description: 'Logo & Company',
    },
    {
        id: 2,
        title: 'Social Links',
        description: 'Social media',
    },
    {
        id: 3,
        title: 'Footer Navigation',
        description: 'Company & Contact',
    },
    {
        id: 4,
        title: 'Newsletter',
        description: 'Subscription',
    },
    {
        id: 5,
        title: 'Bottom Bar',
        description: 'Copyright',
    },
];

const components = {
    1: FooterBrandingSection,
    2: FooterSocialSection,
    3: FooterNavigationSection,
    4: FooterNewsletterSection,
    5: FooterBottomBarSection,
};

const currentComponent = computed(() => components[activeTab.value]);

const handleLoading = (status) => {
    contentLoading.value = status;
};

const changeTab = async (id) => {
    if (activeTab.value === id) return;

    activeTab.value = id;
    const changeTab = (id) => {
        activeTab.value = id;
    };
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6">

        <div class="mb-6 flex items-center justify-between rounded-2xl bg-white p-6 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Footer Management
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    Configure footer branding, navigation, newsletter and bottom bar.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">

            <div class="col-span-3">
                <div class="space-y-3 rounded-2xl bg-white p-4 shadow-sm">

                    <button v-for="tab in tabs" :key="tab.id" @click="changeTab(tab.id)" :class="[
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
                                : 'text-gray-400'">
                                {{ tab.description }}
                            </div>
                        </div>
                    </button>

                </div>
            </div>


            <div class="relative col-span-9 rounded-2xl bg-white p-6 shadow-sm">

                <LoadingOverlay :show="tabLoading || contentLoading" message="Loading footer..." :fullScreen="false" />

                <KeepAlive>
                    <component :is="currentComponent" :key="activeTab" @loading="handleLoading" />
                </KeepAlive>

            </div>

        </div>

    </div>
</template>