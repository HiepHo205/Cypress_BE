<script setup>
import { Image as ImageIcon } from 'lucide-vue-next';

defineProps({
    caseStudy: {
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
    <div class="w-full rounded-xl border border-gray-200 bg-white px-10 py-8">
        <div
            class="flex items-start justify-between gap-8 border-b border-gray-200 pb-6"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50"
                >
                    <img
                        v-if="caseStudy.logo"
                        :src="caseStudy.logo"
                        alt=""
                        class="h-full w-full object-contain"
                    />

                    <ImageIcon v-else :size="22" class="text-gray-300" />
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        {{ caseStudy.clientName || 'Client name' }}
                    </h2>

                    <p class="mt-1 text-xs text-gray-400">
                        {{ caseStudy.author || 'Author' }}
                    </p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-xs text-gray-400">
                    {{ caseStudy.date || 'No date' }}
                </p>

                <div
                    v-if="caseStudy.series?.length"
                    class="mt-2 flex justify-end gap-2"
                >
                    <span
                        v-for="series in caseStudy.series"
                        :key="series"
                        class="text-sm font-semibold text-[#2874d0]"
                    >
                        {{ series }}
                    </span>
                </div>

                <div class="mt-2 flex justify-end gap-2">
                    <template
                        v-for="(category, index) in caseStudy.categories"
                        :key="category + index"
                    >
                        <span
                            v-if="category"
                            class="rounded-full bg-blue-50 px-3 py-1 text-[10px] font-medium text-[#2874d0]"
                        >
                            {{ category }}
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <div
            class="mx-auto mt-8 max-w-[900px] rounded-xl border border-gray-200 p-6"
        >
            <h2 class="text-sm font-semibold text-[#2874d0]">
                Table of Content
            </h2>

            <div class="mt-5 space-y-4">
                <div
                    v-for="(item, index) in tableOfContents"
                    :key="item.id ?? index"
                >
                    <p class="text-sm font-semibold text-gray-800">
                        {{ index + 1 }}.

                        {{ item.title }}
                    </p>

                    <div
                        v-if="item.children?.length"
                        class="mt-2 space-y-1.5 pl-5"
                    >
                        <p
                            v-for="(child, childIndex) in item.children"
                            :key="childIndex"
                            class="text-sm text-gray-500"
                        >
                            {{ index + 1 }}.{{ childIndex + 1 }}

                            {{ child }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="caseStudy.description" class="mx-auto mt-8 max-w-[900px]">
            <h2 class="mb-3 text-lg font-semibold text-gray-800">
                Description
            </h2>

            <p class="whitespace-pre-line text-sm leading-7 text-gray-500">
                {{ caseStudy.description }}
            </p>
        </div>

        <div class="mx-auto mt-8 max-w-[900px]">
            <article
                v-for="(section, index) in sections"
                :key="section.id ?? index"
                class="mb-8"
            >
                <h2 class="mb-3 text-lg font-semibold text-gray-800">
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
    </div>
</template>
