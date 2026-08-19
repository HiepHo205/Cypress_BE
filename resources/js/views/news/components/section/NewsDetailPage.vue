<script setup>
import {
    computed,
    watch
} from 'vue';

import {
    useRoute,
    useRouter
} from 'vue-router';

import { useNews } from '@/composables/news/useNews';
import { useHomepage } from '../../../../composables/home/useHomepage.ts';

import NewsSocialMedia from './NewsSocialMedia.vue';
import NewsDetail from './NewsDetail.vue';

const route = useRoute();
const router = useRouter();

const {
    news: newsData,
    categories,
    loading: newsLoading
} = useNews();

const {
    homepage,
    loading: homepageLoading
} = useHomepage();

const loading = computed(() => {
    return (
        newsLoading.value ||
        homepageLoading.value
    );
});

const currentNews = computed(() => {
    const id = String(
        route.params.id || ''
    ).trim();

    if (!id) {
        return null;
    }

    const data = newsData.value;

    if (!data) {
        return null;
    }

    if (Array.isArray(data)) {
        return (
            data.find(
                (item) =>
                    String(item?.id) === id
            ) ?? null
        );
    }

    if (Array.isArray(data.latest)) {
        return (
            data.latest.find(
                (item) =>
                    String(item?.id) === id
            ) ?? null
        );
    }

    if (Array.isArray(data.items)) {
        return (
            data.items.find(
                (item) =>
                    String(item?.id) === id
            ) ?? null
        );
    }

    if (Array.isArray(data.news)) {
        return (
            data.news.find(
                (item) =>
                    String(item?.id) === id
            ) ?? null
        );
    }

    if (String(data.id) === id) {
        return data;
    }

    return null;
});

const socialMediaData = computed(() => {
    const news = currentNews.value;

    if (!news) {
        return {
            social_media: []
        };
    }

    return {
        ...news,
        social_media:
            Array.isArray(
                news.social_media
            )
                ? news.social_media
                : []
    };
});

watch(
    currentNews,
    (value) => {
        console.log(
            '========== CURRENT NEWS =========='
        );

        console.log(
            'NEWS ID:',
            route.params.id
        );

        console.log(
            'CURRENT NEWS:',
            value
        );

        console.log(
            'SOCIAL MEDIA:',
            value?.social_media
        );

        console.log(
            'SOCIAL MEDIA LENGTH:',
            value?.social_media?.length
        );

        console.log(
            '==================================='
        );
    },
    {
        immediate: true,
        deep: true
    }
);

watch(
    socialMediaData,
    (value) => {
        console.log(
            '========== SOCIAL MEDIA DATA =========='
        );

        console.log(
            'SOCIAL MEDIA DATA:',
            value
        );

        console.log(
            'SOCIAL MEDIA:',
            value?.social_media
        );

        console.log(
            'SOCIAL MEDIA LENGTH:',
            value?.social_media?.length
        );

        console.log(
            '========================================'
        );
    },
    {
        immediate: true,
        deep: true
    }
);

const tabs = [
    {
        id: 'banner',
        title: 'News Banner',
        description:
            'Manage news page banner.',
        component: NewsSocialMedia
    },
    {
        id: 'news',
        title: 'News',
        description:
            'Manage news articles.',
        component: NewsDetail
    }
];

const activeTab = computed(() => {
    const tab = String(
        route.query.tab || 'banner'
    );

    return tabs.some(
        (item) => item.id === tab
    )
        ? tab
        : 'banner';
});

const changeTab = async (id) => {
    if (
        id === activeTab.value
    ) {
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
        <div
            class="mb-6 rounded-2xl bg-white p-4 shadow-sm"
        >
            <div
                class="flex flex-wrap gap-2"
            >
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
                    @click="
                        changeTab(tab.id)
                    "
                >
                    {{ tab.title }}
                </button>
            </div>
        </div>

        <div
            class="rounded-2xl bg-white p-6 shadow-sm"
        >
            <KeepAlive>
                <NewsSocialMedia
                    v-if="
                        activeTab === 'banner'
                    "
                    :key="'banner'"
                    :data="socialMediaData"
                    :homepage="homepage"
                    :loading="loading"
                />

                <NewsDetail
                    v-else-if="
                        activeTab === 'news'
                    "
                    :key="
                        `news-${
                            currentNews?.id ??
                            'empty'
                        }`
                    "
                    :news="currentNews"
                    :categories="categories"
                    :homepage="homepage"
                    :loading="loading"
                />
            </KeepAlive>

            <div
                v-if="
                    activeTab === 'news' &&
                    loading
                "
                class="py-10 text-center text-sm text-gray-500"
            >
                Loading news...
            </div>
        </div>
    </div>
</template>