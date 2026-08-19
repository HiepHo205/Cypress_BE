<script setup>
import { computed, ref, watch } from 'vue';
import { Eye, ArrowLeft } from 'lucide-vue-next';
import { useRoute, useRouter } from 'vue-router';
import { useNews } from '@/composables/news/useNews';

import NewsInformation from './NewsInformation.vue';
import NewsTableOfContents from './NewsTableOfContents.vue';
import NewsContent from './NewsContent.vue';
import NewsPreview from './NewsPreview.vue';

import { useToast } from 'vue-toastification';

defineOptions({
    name: 'NewsDetail'
});

const { success, error, warning } = useToast();

const route = useRoute();
const router = useRouter();

const props = defineProps({
    news: {
        type: Object,
        default: null
    },
    categories: {
        type: [Array, String],
        default: () => []
    },
    homepage: {
        type: Object,
        default: null
    },
    loading: {
        type: Boolean,
        default: false
    }
});

const newsId = computed(() => {
    return String(route.params.id || '').trim();
});

const { updateItem } = useNews('news');

const news = ref({
    id: '',
    title: '',
    date: '',
    author: '',
    image: '',
    description: '',
    categories: '',
    social_media: [],
    imageFile: null
});

const tableOfContents = ref([]);

const sections = ref([]);

const isPreview = ref(false);
const isSaving = ref(false);
const isSavingTableOfContents = ref(false);
const contentEnabled = ref(false);

const parseJson = (value) => {
    if (!value) {
        return null;
    }

    if (typeof value === 'object') {
        return value;
    }

    if (typeof value === 'string') {
        try {
            return JSON.parse(value);
        } catch {
            return null;
        }
    }

    return null;
};

const normalizeImage = (image) => {
    if (!image) {
        return '';
    }

    if (typeof image === 'string') {
        return image;
    }

    return image?.url ?? '';
};

const availableCategories = computed(() => {
    let categories = props.categories;

    if (typeof categories === 'string') {
        categories = parseJson(categories);
    }

    if (!Array.isArray(categories)) {
        return [];
    }

    return categories;
});

const normalizeCategory = (value) => {
    if (!value) {
        return '';
    }

    if (typeof value === 'string') {
        return value.trim();
    }

    if (Array.isArray(value)) {
        if (!value.length) {
            return '';
        }

        return normalizeCategory(value[0]);
    }

    if (typeof value === 'object') {
        return String(
            value.name ??
                value.title ??
                value.category ??
                value.label ??
                ''
        ).trim();
    }

    return String(value).trim();
};

const createTocId = (index = 0) => {
    return `toc-${Date.now()}-${index}-${Math.random()
        .toString(36)
        .slice(2, 8)}`;
};

const createSectionId = (index = 0, childIndex = 0) => {
    return `content-${Date.now()}-${index}-${childIndex}-${Math.random()
        .toString(36)
        .slice(2, 8)}`;
};

const normalizeTableOfContents = (data) => {
    let rawToc =
        data?.tableOfContents ??
        data?.table_of_contents ??
        [];

    rawToc = parseJson(rawToc);

    if (!Array.isArray(rawToc) || !rawToc.length) {
        contentEnabled.value = false;

        return [
            {
                id: createTocId(),
                title: '',
                children: ['']
            }
        ];
    }

    const normalized = rawToc.map((item, index) => ({
        id: item?.id ?? createTocId(index),
        title: String(
            item?.title ??
                item?.name ??
                ''
        ).trim(),
        children: Array.isArray(item?.children)
            ? item.children
                  .map((child) => {
                      if (typeof child === 'string') {
                          return child.trim();
                      }

                      return String(
                          child?.title ??
                              child?.name ??
                              ''
                      ).trim();
                  })
                  .filter(Boolean)
            : []
    }));

    contentEnabled.value = normalized.some(
        (item) =>
            item.title ||
            item.children.length
    );

    return normalized;
};

const normalizeSections = (data) => {
    let rawSections = data?.sections ?? [];

    rawSections = parseJson(rawSections);

    if (!Array.isArray(rawSections)) {
        return [];
    }

    return rawSections.map((section, index) => {
        const content =
            section?.content ??
            section?.description ??
            '';

        const description =
            section?.description ??
            section?.content ??
            '';

        return {
            id:
                section?.id ??
                createSectionId(index),

            type:
                section?.type ??
                'text',

            title:
                section?.title ??
                section?.heading ??
                '',

            heading:
                section?.heading ??
                section?.title ??
                '',

            content,

            description,

            image:
                normalizeImage(
                    section?.image
                ),

            imageFile: null,

            tocKey:
                section?.tocKey ??
                null,

            tocId:
                section?.tocId ??
                null,

            tocIndex:
                section?.tocIndex ??
                null,

            childIndex:
                section?.childIndex ??
                null,

            order:
                section?.order ??
                index + 1
        };
    });
};

const syncSectionsWithTableOfContents = () => {
    const currentSections = Array.isArray(
        sections.value
    )
        ? sections.value
        : [];

    if (!currentSections.length) {
        return;
    }

    const existingByTocKey = new Map();
    const existingByTitle = new Map();

    currentSections.forEach((section) => {
        if (section?.tocKey) {
            existingByTocKey.set(
                `toc:${section.tocKey}`,
                section
            );
        }

        const title = String(
            section?.title ??
                section?.heading ??
                ''
        ).trim();

        if (title) {
            existingByTitle.set(
                `title:${title}`,
                section
            );
        }
    });

    const syncedSections = [...currentSections];

    tableOfContents.value.forEach(
        (tocItem, sectionIndex) => {
            const children = Array.isArray(
                tocItem?.children
            )
                ? tocItem.children
                : [];

            children.forEach(
                (child, childIndex) => {
                    const title = String(
                        child ?? ''
                    ).trim();

                    if (!title) {
                        return;
                    }

                    const tocKey =
                        `${tocItem.id}-${childIndex}`;

                    const existing =
                        existingByTocKey.get(
                            `toc:${tocKey}`
                        ) ??
                        existingByTitle.get(
                            `title:${title}`
                        );

                    if (!existing) {
                        return;
                    }

                    const existingIndex =
                        syncedSections.findIndex(
                            (section) =>
                                String(
                                    section?.id
                                ) ===
                                String(
                                    existing?.id
                                )
                        );

                    if (existingIndex === -1) {
                        return;
                    }

                    const existingSection =
                        syncedSections[
                            existingIndex
                        ];

                    const content =
                        existingSection?.content ??
                        existingSection?.description ??
                        '';

                    const description =
                        existingSection?.description ??
                        existingSection?.content ??
                        '';

                    syncedSections[
                        existingIndex
                    ] = {
                        ...existingSection,

                        tocKey,

                        tocId:
                            tocItem.id,

                        tocIndex:
                            sectionIndex + 1,

                        childIndex:
                            childIndex + 1,

                        title:
                            existingSection
                                ?.title ||
                            title,

                        heading:
                            existingSection
                                ?.heading ||
                            existingSection
                                ?.title ||
                            title,

                        content,

                        description,

                        image:
                            existingSection
                                ?.image ??
                            null,

                        imageFile:
                            existingSection
                                ?.imageFile ??
                            null
                    };
                }
            );
        }
    );

    sections.value =
        syncedSections;
};

const normalizeDetail = (data) => {
    const mergedData =
        parseJson(data);

    if (
        !mergedData ||
        typeof mergedData !== 'object'
    ) {
        return;
    }

    const returnedId = String(
        mergedData.id ??
            mergedData.newsId ??
            mergedData.news_id ??
            newsId.value ??
            ''
    ).trim();

    const image =
        normalizeImage(
            mergedData.image
        ) ||
        normalizeImage(
            mergedData.thumbnail
        ) ||
        normalizeImage(
            mergedData.coverImage
        ) ||
        normalizeImage(
            mergedData.cover_image
        );

    const socialMedia =
        Array.isArray(
            mergedData.social_media
        )
            ? mergedData.social_media
            : Array.isArray(
                  mergedData.socialMedia
              )
              ? mergedData.socialMedia
              : [];

    const categoryValue =
        mergedData.categories ??
        mergedData.category ??
        '';

    const normalizedNews = {
        id:
            returnedId ||
            newsId.value,

        title:
            mergedData.title ??
            '',

        date:
            mergedData.date ??
            mergedData.publishedAt ??
            mergedData.published_at ??
            '',

        author:
            mergedData.author ??
            mergedData.authorName ??
            mergedData.author_name ??
            '',

        image,

        description:
            mergedData.description ??
            mergedData.content ??
            '',

        categories:
            normalizeCategory(
                categoryValue
            ),

        social_media:
            socialMedia,

        imageFile: null
    };

    const normalizedToc =
        normalizeTableOfContents(
            mergedData
        );

    const normalizedSections =
        normalizeSections(
            mergedData
        );

    news.value =
        normalizedNews;

    tableOfContents.value =
        normalizedToc;

    sections.value =
        normalizedSections;

    if (
        normalizedSections.length > 0
    ) {
        syncSectionsWithTableOfContents();
    }
};

const loadNews = (source = null) => {
    const id = newsId.value;

    if (!id) {
        return;
    }

    const selectedNews =
        source &&
        typeof source === 'object'
            ? source
            : props.news;

    if (
        !selectedNews ||
        typeof selectedNews !== 'object'
    ) {
        return;
    }

    const mergedData = {
        ...selectedNews,
        id
    };

    if (
        !Array.isArray(
            mergedData.social_media
        )
    ) {
        mergedData.social_media =
            [];
    }

    normalizeDetail(
        mergedData
    );
};

watch(
    () => props.news,
    (value) => {
        if (
            !value ||
            typeof value !== 'object'
        ) {
            return;
        }

        loadNews(value);
    },
    {
        immediate: true
    }
);

watch(
    () => route.params.id,
    () => {
        if (!newsId.value) {
            return;
        }

        isPreview.value = false;
        contentEnabled.value = false;

        loadNews();
    },
    {
        immediate: true
    }
);

const addTableOfContent = () => {
    tableOfContents.value.push({
        id: createTocId(
            tableOfContents.value.length
        ),
        title: '',
        children: ['']
    });
};

const removeTableOfContent = (
    index
) => {
    if (
        index < 0 ||
        index >=
            tableOfContents.value.length
    ) {
        return;
    }

    tableOfContents.value.splice(
        index,
        1
    );
};

const addSubItem = (index) => {
    const item =
        tableOfContents.value[index];

    if (!item) {
        return;
    }

    if (
        !Array.isArray(
            item.children
        )
    ) {
        item.children = [];
    }

    item.children.push('');
};

const removeSubItem = (
    parentIndex,
    childIndex
) => {
    const item =
        tableOfContents.value[
            parentIndex
        ];

    if (!item?.children) {
        return;
    }

    if (
        childIndex < 0 ||
        childIndex >=
            item.children.length
    ) {
        return;
    }

    item.children.splice(
        childIndex,
        1
    );
};

const addSection = () => {
    if (!contentEnabled.value) {
        warning(
            'Please save Table of Content first.'
        );

        return;
    }

    sections.value.push({
        id: createSectionId(
            sections.value.length
        ),
        type: 'text',
        title: '',
        heading: '',
        description: '',
        content: '',
        image: null,
        imageFile: null
    });
};

const removeSection = (
    index
) => {
    if (!contentEnabled.value) {
        return;
    }

    if (
        index < 0 ||
        index >= sections.value.length
    ) {
        return;
    }

    const section =
        sections.value[index];

    if (
        section?.image &&
        typeof section.image ===
            'string' &&
        section.image.startsWith(
            'blob:'
        )
    ) {
        URL.revokeObjectURL(
            section.image
        );
    }

    sections.value.splice(
        index,
        1
    );
};

const moveSectionUp = (
    index
) => {
    if (
        !contentEnabled.value ||
        index <= 0 ||
        index >=
            sections.value.length
    ) {
        return;
    }

    const current =
        sections.value[index];

    sections.value[index] =
        sections.value[
            index - 1
        ];

    sections.value[
        index - 1
    ] = current;
};

const moveSectionDown = (
    index
) => {
    if (
        !contentEnabled.value ||
        index < 0 ||
        index >=
            sections.value.length - 1
    ) {
        return;
    }

    const current =
        sections.value[index];

    sections.value[index] =
        sections.value[
            index + 1
        ];

    sections.value[
        index + 1
    ] = current;
};

const handleImageUpload = (
    event,
    section
) => {
    if (
        !contentEnabled.value ||
        !section
    ) {
        return;
    }

    const file =
        event?.target?.files?.[0];

    if (event?.target) {
        event.target.value = '';
    }

    if (!file) {
        return;
    }

    if (
        ![
            'image/png',
            'image/jpeg',
            'image/webp'
        ].includes(file.type)
    ) {
        warning(
            'Only PNG, JPEG and WebP images are allowed.'
        );

        return;
    }

    if (
        file.size >
        5 * 1024 * 1024
    ) {
        warning(
            'Image size must be less than 5MB.'
        );

        return;
    }

    if (
        typeof section.image ===
            'string' &&
        section.image.startsWith(
            'blob:'
        )
    ) {
        URL.revokeObjectURL(
            section.image
        );
    }

    const index =
        sections.value.findIndex(
            (item) =>
                String(item?.id) ===
                String(section?.id)
        );

    if (index === -1) {
        return;
    }

    const updatedSections = [
        ...sections.value
    ];

    updatedSections[index] = {
        ...updatedSections[index],
        imageFile: file,
        image:
            URL.createObjectURL(
                file
            )
    };

    sections.value =
        updatedSections;
};

const removeImage = (
    section
) => {
    if (
        !contentEnabled.value ||
        !section
    ) {
        return;
    }

    if (
        section.image &&
        typeof section.image ===
            'string' &&
        section.image.startsWith(
            'blob:'
        )
    ) {
        URL.revokeObjectURL(
            section.image
        );
    }

    section.image = null;
    section.imageFile = null;
};

const handleNewsImageUpload = (
    event
) => {
    const file =
        event?.target?.files?.[0];

    if (event?.target) {
        event.target.value = '';
    }

    if (!file) {
        return;
    }

    if (
        !file.type.startsWith(
            'image/'
        )
    ) {
        warning(
            'Please select an image file.'
        );

        return;
    }

    if (
        file.size >
        5 * 1024 * 1024
    ) {
        warning(
            'Image size must be less than 5MB.'
        );

        return;
    }

    if (
        news.value.image &&
        typeof news.value.image ===
            'string' &&
        news.value.image.startsWith(
            'blob:'
        )
    ) {
        URL.revokeObjectURL(
            news.value.image
        );
    }

    news.value.image =
        URL.createObjectURL(
            file
        );

    news.value.imageFile =
        file;
};

const removeNewsImage = () => {
    if (
        news.value.image &&
        typeof news.value.image ===
            'string' &&
        news.value.image.startsWith(
            'blob:'
        )
    ) {
        URL.revokeObjectURL(
            news.value.image
        );
    }

    news.value.image = '';
    news.value.imageFile = null;
};

const goBack = async () => {
    await router.push({
        name: 'news.management',
        query: {
            tab: 'news'
        }
    });
};

const saveTableOfContent = async (
    items
) => {
    const id = String(
        route.params.id || ''
    ).trim();

    if (
        !id ||
        isSavingTableOfContents.value
    ) {
        return;
    }

    const sourceItems =
        Array.isArray(items)
            ? items
            : [];

    const normalizedTableOfContents =
        sourceItems.map(
            (item, index) => ({
                id:
                    item?.id ??
                    createTocId(index),

                order:
                    index + 1,

                title: String(
                    item?.title ??
                        ''
                ).trim(),

                children:
                    Array.isArray(
                        item?.children
                    )
                        ? item.children
                              .map(
                                  (
                                      child
                                  ) =>
                                      String(
                                          child ??
                                              ''
                                      ).trim()
                              )
                              .filter(
                                  Boolean
                              )
                        : []
            })
        );

    const hasValidContent =
        normalizedTableOfContents.some(
            (item) =>
                item.title ||
                item.children.length
        );

    if (!hasValidContent) {
        contentEnabled.value =
            false;

        warning(
            'Please enter at least one Table of Content item.'
        );

        return;
    }

    const hasEmptyTitle =
        normalizedTableOfContents.some(
            (item) =>
                !item.title
        );

    if (hasEmptyTitle) {
        contentEnabled.value =
            false;

        warning(
            'Please enter a title for every Table of Content section.'
        );

        return;
    }

    isSavingTableOfContents.value =
        true;

    try {
        await updateItem({
            id,
            tableOfContents:
                normalizedTableOfContents
        });

        tableOfContents.value =
            normalizedTableOfContents;

        if (
            sections.value.length
        ) {
            syncSectionsWithTableOfContents();
        }

        contentEnabled.value =
            true;

        success(
            'Table of Content saved successfully.'
        );
    } catch (err) {
        console.error(
            'SAVE TOC ERROR:',
            err
        );

        contentEnabled.value =
            false;

        error(
            'Failed to save Table of Content. Please try again.'
        );
    } finally {
        isSavingTableOfContents.value =
            false;
    }
};

const saveNews = async () => {
    const id = String(
        route.params.id || ''
    ).trim();

    if (
        !id ||
        isSaving.value
    ) {
        return;
    }

    isSaving.value = true;

    try {
        const images = [];

        const normalizedSections =
            sections.value.map(
                (section) => {
                    const result = {
                        id:
                            section?.id,

                        type:
                            section?.type ??
                            'text',

                        title:
                            section?.title?.trim() ??
                            '',

                        content:
                            section?.content?.trim() ??
                            '',

                        image:
                            section?.image ??
                            null
                    };

                    if (
                        section?.imageFile instanceof
                        File
                    ) {
                        const imageIndex =
                            images.length;

                        images.push(
                            section.imageFile
                        );

                        result.image = {
                            url: '',
                            public_id: '',
                            _image_index:
                                imageIndex
                        };
                    }

                    return result;
                }
            );

        let image =
            news.value.image ||
            '';

        if (
            news.value.imageFile instanceof
            File
        ) {
            const imageIndex =
                images.length;

            images.push(
                news.value.imageFile
            );

            image = {
                url: '',
                public_id: '',
                _image_index:
                    imageIndex
            };
        }

        const normalizedTableOfContents =
            tableOfContents.value.map(
                (item, index) => ({
                    id:
                        item?.id ??
                        createTocId(
                            index
                        ),

                    order:
                        index + 1,

                    title:
                        item?.title?.trim() ??
                        '',

                    children:
                        Array.isArray(
                            item?.children
                        )
                            ? item.children
                                  .map(
                                      (
                                          child
                                      ) =>
                                          String(
                                              child ??
                                                  ''
                                          ).trim()
                                  )
                                  .filter(
                                      Boolean
                                  )
                            : []
                })
            );

        const category =
            normalizeCategory(
                news.value.categories
            );

        const payload = {
            id,

            title:
                news.value.title?.trim() ??
                '',

            date:
                news.value.date ??
                '',

            author:
                news.value.author?.trim() ??
                '',

            image,

            categories: category
                ? [category]
                : [],

            description:
                news.value.description?.trim() ??
                '',

            social_media:
                Array.isArray(
                    news.value.social_media
                )
                    ? news.value
                          .social_media
                    : [],

            tableOfContents:
                normalizedTableOfContents,

            sections:
                normalizedSections
        };

        await updateItem(
            payload,
            images
        );

        success(
            'News saved successfully.'
        );
    } catch (err) {
        console.error(
            'SAVE NEWS ERROR:',
            err
        );

        error(
            'Failed to save News. Please try again.'
        );
    } finally {
        isSaving.value = false;
    }
};
</script>

<template>
    <div
        class="min-h-screen w-full bg-[#f5f7fb] px-6 py-6"
    >
        <div class="w-full">
            <div
                class="mb-5 flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-5 py-3"
            >
                <div
                    class="flex items-center gap-3"
                >
                    <button
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50"
                        @click="goBack"
                    >
                        <ArrowLeft
                            :size="17"
                        />
                    </button>

                    <div>
                        <h1
                            class="text-[17px] font-semibold text-gray-800"
                        >
                            News Detail
                        </h1>

                        <p
                            class="mt-0.5 text-[11px] text-gray-400"
                        >
                            {{ newsId }}
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center gap-2"
                >
                    <button
                        type="button"
                        class="flex h-9 items-center gap-2 rounded-lg border border-gray-200 px-4 text-[12px] font-medium text-gray-600 transition hover:bg-gray-50"
                        @click="
                            isPreview =
                                !isPreview
                        "
                    >
                        <Eye
                            :size="15"
                        />

                        {{
                            isPreview
                                ? 'Edit'
                                : 'Preview'
                        }}
                    </button>
                </div>
            </div>

            <div
                v-if="loading"
                class="mb-5 w-full rounded-xl border border-gray-200 bg-white px-5 py-10 text-center"
            >
                <p
                    class="text-sm text-gray-500"
                >
                    Loading news...
                </p>
            </div>

            <div
                v-else-if="!news.id"
                class="mb-5 w-full rounded-xl border border-dashed border-gray-300 bg-white px-5 py-10 text-center"
            >
                <p
                    class="text-sm text-gray-500"
                >
                    News not found.
                </p>
            </div>

            <div
                v-else
                class="w-full"
            >
                <NewsPreview
                    v-if="isPreview"
                    :news="news"
                    :table-of-contents="
                        tableOfContents
                    "
                    :sections="sections"
                />

                <div
                    v-else
                    class="grid w-full grid-cols-1 items-start gap-5 xl:grid-cols-3"
                >
                    <NewsInformation
                        v-model:news="
                            news
                        "
                        :categories="
                            availableCategories
                        "
                        :saving="
                            isSaving
                        "
                        @upload-image="
                            handleNewsImageUpload
                        "
                        @remove-image="
                            removeNewsImage
                        "
                    />

                    <div
                        class="min-w-0 space-y-5 xl:col-span-2"
                    >
                        <NewsTableOfContents
                            v-model="
                                tableOfContents
                            "
                            :saving="
                                isSavingTableOfContents
                            "
                            @add="
                                addTableOfContent
                            "
                            @remove="
                                removeTableOfContent
                            "
                            @add-sub="
                                addSubItem
                            "
                            @remove-sub="
                                removeSubItem
                            "
                            @save="
                                saveTableOfContent
                            "
                        />

                        <NewsContent
                            v-model="
                                sections
                            "
                            :saving="
                                isSaving
                            "
                            @add="
                                addSection
                            "
                            @remove="
                                removeSection
                            "
                            @move-up="
                                moveSectionUp
                            "
                            @move-down="
                                moveSectionDown
                            "
                            @upload-image="
                                handleImageUpload
                            "
                            @remove-image="
                                removeImage
                            "
                            @save="
                                saveNews
                            "
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>