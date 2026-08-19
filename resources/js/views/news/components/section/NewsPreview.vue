<script setup>
import { Image as ImageIcon } from 'lucide-vue-next';

defineProps({
    news: {
        type: Object,
        required: true
    },

    tableOfContents: {
        type: Array,
        default: () => []
    },

    sections: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <div
        class="w-full rounded-xl border border-gray-200 bg-white px-10 py-8"
    >
        <!-- News Header -->
        <div
            class="border-b border-gray-200 pb-6"
        >
            <div class="flex items-start justify-between gap-8">
                <div class="min-w-0">
                    <h1
                        class="text-2xl font-semibold leading-9 text-gray-800"
                    >
                        {{ news.title || 'News title' }}
                    </h1>

                    <div
                        class="mt-3 flex flex-wrap items-center gap-3"
                    >
                        <span
                            class="text-xs text-gray-400"
                        >
                            {{ news.date || 'No date' }}
                        </span>

                        <span
                            v-if="news.author"
                            class="text-xs text-gray-400"
                        >
                            •
                        </span>

                        <span
                            v-if="news.author"
                            class="text-xs font-medium text-gray-500"
                        >
                            {{ news.author }}
                        </span>
                    </div>

                    <div
                        v-if="news.categories?.length"
                        class="mt-3 flex flex-wrap gap-2"
                    >
                        <span
                            v-for="(category, index) in news.categories"
                            :key="category + index"
                            v-if="category"
                            class="rounded-full bg-blue-50 px-3 py-1 text-[10px] font-medium text-[#2874d0]"
                        >
                            {{ category }}
                        </span>
                    </div>
                </div>

                <div
                    v-if="news.image"
                    class="h-24 w-36 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-gray-50"
                >
                    <img
                        :src="news.image"
                        alt="News cover"
                        class="h-full w-full object-cover"
                    />
                </div>

                <div
                    v-else
                    class="flex h-24 w-36 shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-gray-50"
                >
                    <ImageIcon
                        :size="24"
                        class="text-gray-300"
                    />
                </div>
            </div>
        </div>

        <!-- Cover Image -->
        <div
            v-if="news.image"
            class="mx-auto mt-8 max-w-[900px] overflow-hidden rounded-xl"
        >
            <img
                :src="news.image"
                alt="News cover"
                class="max-h-[500px] w-full object-cover"
            />
        </div>

        <!-- Table of Contents -->
        <div
            v-if="tableOfContents.length"
            class="mx-auto mt-8 max-w-[900px] rounded-xl border border-gray-200 p-6"
        >
            <h2
                class="text-sm font-semibold text-[#2874d0]"
            >
                Table of Content
            </h2>

            <div class="mt-5 space-y-4">
                <div
                    v-for="(item, index) in tableOfContents"
                    :key="item.id ?? index"
                >
                    <p
                        class="text-sm font-semibold text-gray-800"
                    >
                        {{ index + 1 }}.

                        {{ item.title }}
                    </p>

                    <div
                        v-if="item.children?.length"
                        class="mt-2 space-y-1.5 pl-5"
                    >
                        <p
                            v-for="(
                                child, childIndex
                            ) in item.children"
                            :key="childIndex"
                            class="text-sm text-gray-500"
                        >
                            {{ index + 1 }}.{{
                                childIndex + 1
                            }}

                            {{ child }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div
            v-if="news.description"
            class="mx-auto mt-8 max-w-[900px]"
        >
            <h2
                class="mb-3 text-lg font-semibold text-gray-800"
            >
                Description
            </h2>

            <p
                class="whitespace-pre-line text-sm leading-7 text-gray-500"
            >
                {{ news.description }}
            </p>
        </div>

        <!-- Content Sections -->
        <div
            v-if="sections.length"
            class="mx-auto mt-8 max-w-[900px]"
        >
            <article
                v-for="(section, index) in sections"
                :key="section.id ?? index"
                class="mb-8"
            >
                <h2
                    v-if="section.title"
                    class="mb-3 text-lg font-semibold text-gray-800"
                >
                    {{ index + 1 }}.

                    {{ section.title }}
                </h2>

                <p
                    v-if="section.content"
                    class="mb-4 whitespace-pre-line text-sm leading-7 text-gray-500"
                >
                    {{ section.content }}
                </p>

                <img
                    v-if="section.image"
                    :src="section.image"
                    alt=""
                    class="max-h-[500px] w-full rounded-xl object-cover"
                />
            </article>
        </div>

        <!-- Empty Content -->
        <div
            v-if="
                !news.description &&
                !sections.length
            "
            class="mx-auto mt-8 max-w-[900px] rounded-xl border border-dashed border-gray-200 py-10 text-center"
        >
            <p class="text-sm text-gray-400">
                No content available.
            </p>
        </div>
    </div>
</template>