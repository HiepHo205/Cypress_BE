<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import SocialMedia from './SocialMedia.vue';
import CaseStudyDetail from './CaseStudyDetail.vue';

import { useCaseStudy } from '@/composables/caseStudy/useCaseStudy';
import { useHomepage } from '../../../../composables/home/useHomepage.ts';

const route = useRoute();
const router = useRouter();

const {
    caseStudies,
    caseStudyDetail: socialMediaDetail,
    categories,
    loading: caseStudyLoading
} = useCaseStudy();

const { homepage, loading: homepageLoading } = useHomepage();

const loading = computed(() => {
    return caseStudyLoading.value || homepageLoading.value;
});

const currentCaseStudy = computed(() => {
    const id = route.params.id;

    if (!id) {
        return null;
    }

    return (
        caseStudies.value.find((item) => String(item.id) === String(id)) ?? null
    );
});

const tabs = [
    {
        id: 'banner',
        title: 'Case Study Banner',
        description: 'Manage case study page banner.',
        component: SocialMedia
    },
    {
        id: 'case-studies',
        title: 'Case Studies',
        description: 'Manage case study articles.',
        component: CaseStudyDetail
    }
];

const activeTab = computed(() => {
    const tab = String(route.query.tab || 'banner');

    return tabs.some((item) => item.id === tab) ? tab : 'banner';
});

const currentTab = computed(() => {
    return tabs.find((tab) => tab.id === activeTab.value) ?? tabs[0];
});

const currentComponent = computed(() => {
    return currentTab.value.component;
});

const changeTab = async (id) => {
    if (id === activeTab.value) {
        return;
    }

    await router.push({
        query: {
            ...route.query,
            tab: id
        }
    });
};
</script>

<template>
    <div class="w-full">
        <div class="mb-6 rounded-2xl bg-white p-4 shadow-sm">
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    type="button"
                    class="rounded-xl px-5 py-3 text-sm font-medium transition"
                    :class="
                        activeTab === tab.id
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                    "
                    @click="changeTab(tab.id)"
                >
                    {{ tab.title }}
                </button>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <KeepAlive>
                <SocialMedia
                    v-if="activeTab === 'banner'"
                    :key="'banner'"
                    :data="socialMediaDetail"
                    :homepage="homepage"
                    :loading="loading"
                />

                <CaseStudyDetail
                    v-else-if="activeTab === 'case-studies'"
                    :key="`case-study-${currentCaseStudy?.id ?? 'empty'}`"
                    :case-study="currentCaseStudy"
                    :categories="categories"
                    :homepage="homepage"
                    :loading="loading"
                />
            </KeepAlive>
            <div
                v-if="activeTab === 'case-studies' && loading"
                class="py-10 text-center text-sm text-gray-500"
            >
                Loading case study...
            </div>
        </div>
    </div>
</template>
