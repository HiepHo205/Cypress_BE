<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import FooterBrandingSection from './components/FooterBrandingSection.vue';
import FooterSocialSection from './components/FooterSocialSection.vue';
import FooterNavigationSection from './components/FooterNavigationSection.vue';
import FooterNewsletterSection from './components/FooterNewsletterSection.vue';
import FooterBottomBarSection from './components/FooterBottomBarSection.vue';

import LoadingOverlay from '@/components/common/LoadingOverlay.vue';


const route = useRoute();
const router = useRouter();


const tabLoading = ref(false);
const contentLoading = ref(false);


const tabs = [
    {
        id: 'branding',
        title: 'Branding',
        description: 'Logo & Company'
    },
    {
        id: 'social',
        title: 'Social Links',
        description: 'Social media'
    },
    {
        id: 'navigation',
        title: 'Footer Navigation',
        description: 'Company & Contact'
    },
    {
        id: 'newsletter',
        title: 'Newsletter',
        description: 'Subscription'
    },
    {
        id: 'bottom-bar',
        title: 'Bottom Bar',
        description: 'Copyright'
    }
];


const components = {
    branding: FooterBrandingSection,
    social: FooterSocialSection,
    navigation: FooterNavigationSection,
    newsletter: FooterNewsletterSection,
    'bottom-bar': FooterBottomBarSection
};


const activeTab = computed(() => {
    return route.query.tab || 'branding';
});


const currentComponent = computed(() => {
    return components[activeTab.value] || FooterBrandingSection;
});


onMounted(() => {
    if (!route.query.tab) {
        router.replace({
            path: '/admin/footer',
            query: {
                tab: 'branding'
            }
        });
    }
});


const handleLoading = (status) => {
    contentLoading.value = status;
};


const changeTab = (id) => {
    router.push({
        path: '/admin/footer',
        query: {
            tab: id
        }
    });
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
                            {{ tabs.indexOf(tab) + 1 }}
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