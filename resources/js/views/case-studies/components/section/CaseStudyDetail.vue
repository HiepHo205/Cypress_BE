<script setup>
import { computed, ref, watch, onActivated } from 'vue';
import { Save, Eye, ArrowLeft } from 'lucide-vue-next';
import { useRoute, useRouter } from 'vue-router';
import { useCaseStudy } from '@/composables/caseStudy/useCaseStudy';

import CaseStudyInformation from './CaseStudyInformation.vue';
import CaseStudyTableOfContents from './CaseStudyTableOfContents.vue';
import CaseStudyContent from './CaseStudyContent.vue';
import CaseStudyPreview from './CaseStudyPreview.vue';

import { useToast } from 'vue-toastification';

defineOptions({
    name: 'CaseStudyDetail'
});

const { success, error, warning } = useToast();

const route = useRoute();
const router = useRouter();

const props = defineProps({
    caseStudy: {
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

const caseStudyId = computed(() => {
    return String(route.params.id || '').trim();
});

const { updateItem } = useCaseStudy('caseStudies');

const caseStudy = ref({
    id: '',
    title: '',
    date: '',
    clientName: '',
    author: '',
    logo: '',
    planTitle: '',
    seriesTags: [],
    description: '',
    categories: ['', ''],
    social_media: [],
    logoFile: null
});

const tableOfContents = ref([
    {
        id: Date.now(),
        title: '',
        children: ['']
    }
]);

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

const seriesOptions = computed(() => {
    const caseStudies = props.homepage?.caseStudies?.caseStudies;

    if (!Array.isArray(caseStudies)) {
        return [];
    }

    const series = caseStudies.flatMap((item) => {
        const seriesTags = item?.seriesTags;

        if (!seriesTags) {
            return [];
        }

        if (Array.isArray(seriesTags)) {
            return seriesTags.map((tag) => String(tag).trim()).filter(Boolean);
        }

        return String(seriesTags)
            .split(',')
            .map((tag) => tag.trim())
            .filter(Boolean);
    });

    return [...new Set(series)];
});

const normalizeSeriesTags = (value) => {
    if (!value) {
        return [];
    }

    if (Array.isArray(value)) {
        return value.map((item) => String(item).trim()).filter(Boolean);
    }

    return String(value)
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean);
};

const normalizeCategories = (categories) => {
    if (!Array.isArray(categories)) {
        return ['', ''];
    }

    const result = categories
        .map((item) => {
            if (typeof item === 'string') {
                return item;
            }

            return item?.name ?? item?.title ?? '';
        })
        .filter(Boolean);

    return [result[0] ?? '', result[1] ?? ''];
};

const normalizeTableOfContents = (data) => {
    let rawToc = data?.tableOfContents ?? data?.table_of_contents ?? [];

    if (typeof rawToc === 'string') {
        try {
            rawToc = JSON.parse(rawToc);
        } catch {
            rawToc = [];
        }
    }

    if (!Array.isArray(rawToc) || !rawToc.length) {
        contentEnabled.value = false;

        return [
            {
                id: Date.now(),
                title: '',
                children: ['']
            }
        ];
    }

    contentEnabled.value = true;

    return rawToc.map((item, index) => ({
        id: item?.id ?? `${Date.now()}-${index}`,
        title: item?.title ?? item?.name ?? '',
        children: Array.isArray(item?.children)
            ? item.children
                  .map((child) => {
                      if (typeof child === 'string') {
                          return child;
                      }

                      return child?.title ?? child?.name ?? '';
                  })
                  .filter(Boolean)
            : []
    }));
};

const normalizeSections = (data) => {
    if (!Array.isArray(data?.sections)) {
        return [];
    }

    return data.sections.map((section, index) => ({
        id: section.id ?? `${Date.now()}-${index}`,
        type: section.type ?? 'text',
        title: section.title ?? section.heading ?? '',
        content: section.content ?? section.description ?? '',
        image: normalizeImage(section.image),
        imageFile: null
    }));
};

const syncSectionsWithTableOfContents = () => {
    const currentSections = Array.isArray(sections.value) ? sections.value : [];

    const existingByTocKey = new Map();
    const existingByTitle = new Map();

    currentSections.forEach((section) => {
        if (section?.tocKey) {
            existingByTocKey.set(`toc:${section.tocKey}`, section);
        }

        const title = String(section?.title ?? section?.heading ?? '').trim();

        if (title) {
            existingByTitle.set(`title:${title}`, section);
        }
    });

    const syncedSections = [...currentSections];

    tableOfContents.value.forEach((tocItem, sectionIndex) => {
        const children = Array.isArray(tocItem.children)
            ? tocItem.children
            : [];

        children.forEach((child, childIndex) => {
            const title = String(child ?? '').trim();

            if (!title) {
                return;
            }

            const tocKey = `${tocItem.id}-${childIndex}`;

            const existing =
                existingByTocKey.get(`toc:${tocKey}`) ??
                existingByTitle.get(`title:${title}`);

            if (existing) {
                const existingIndex = syncedSections.findIndex(
                    (section) => section.id === existing.id
                );

                if (existingIndex !== -1) {
                    syncedSections[existingIndex] = {
                        ...syncedSections[existingIndex],
                        tocKey,
                        tocId: tocItem.id,
                        tocIndex: sectionIndex + 1,
                        childIndex: childIndex + 1,
                        title: syncedSections[existingIndex].title || title,
                        heading: syncedSections[existingIndex].heading || title,
                        description:
                            syncedSections[existingIndex].description ??
                            syncedSections[existingIndex].content ??
                            '',
                        content: syncedSections[existingIndex].content ?? '',
                        image: syncedSections[existingIndex].image ?? null,
                        imageFile:
                            syncedSections[existingIndex].imageFile ?? null
                    };
                }

                return;
            }

            syncedSections.push({
                id: `content-${Date.now()}-${sectionIndex}-${childIndex}`,
                type: 'text',
                tocKey,
                tocId: tocItem.id,
                tocIndex: sectionIndex + 1,
                childIndex: childIndex + 1,
                title,
                heading: title,
                description: '',
                content: '',
                image: null,
                imageFile: null
            });
        });
    });

    sections.value = syncedSections;
};

const normalizeDetail = (data) => {
    const mergedData = parseJson(data);

    if (!mergedData || typeof mergedData !== 'object') {
        return;
    }

    const returnedId = String(
        mergedData.id ??
            mergedData.caseStudyId ??
            mergedData.case_study_id ??
            caseStudyId.value ??
            ''
    ).trim();

    const categories = normalizeCategories(mergedData.categories);

    const logo =
        normalizeImage(mergedData.logo) ||
        normalizeImage(mergedData.image) ||
        normalizeImage(mergedData.clientLogo) ||
        normalizeImage(mergedData.client_logo);

    const socialMedia = Array.isArray(mergedData.social_media)
        ? mergedData.social_media
        : Array.isArray(mergedData.socialMedia)
          ? mergedData.socialMedia
          : [];

    caseStudy.value = {
        id: returnedId || caseStudyId.value,

        title: mergedData.title ?? '',

        date:
            mergedData.date ??
            mergedData.publishedAt ??
            mergedData.published_at ??
            '',

        clientName: mergedData.clientName ?? mergedData.client_name ?? '',

        author:
            mergedData.author ??
            mergedData.authorName ??
            mergedData.author_name ??
            '',

        logo,

        planTitle: mergedData.planTitle ?? mergedData.plan_title ?? '',

        seriesTags: normalizeSeriesTags(
            mergedData.seriesTags ?? mergedData.series_tags ?? mergedData.series
        ),

        description: mergedData.description ?? mergedData.content ?? '',

        categories: categories.some(Boolean) ? categories : ['', ''],

        social_media: socialMedia,

        logoFile: null
    };

    tableOfContents.value = normalizeTableOfContents(mergedData);

    sections.value = normalizeSections(mergedData);

    syncSectionsWithTableOfContents();
};

const loadCaseStudy = () => {
    const id = caseStudyId.value;

    if (!id) {
        return;
    }

    const selectedCaseStudy = props.caseStudy;

    if (!selectedCaseStudy || typeof selectedCaseStudy !== 'object') {
        return;
    }

    const mergedData = {
        ...selectedCaseStudy,
        id
    };

    if (!Array.isArray(mergedData.social_media)) {
        mergedData.social_media = [];
    }

    normalizeDetail(mergedData);
};

watch(
    () => props.caseStudy,
    (value) => {
        if (!value) {
            return;
        }

        loadCaseStudy();
    },
    {
        immediate: true,
        deep: true
    }
);

watch(
    () => route.params.id,
    () => {
        if (!caseStudyId.value) {
            return;
        }

        isPreview.value = false;
        contentEnabled.value = false;

        loadCaseStudy();
    },
    {
        immediate: true
    }
);

onActivated(() => {
    if (!caseStudyId.value) {
        return;
    }

    if (props.caseStudy && typeof props.caseStudy === 'object') {
        loadCaseStudy();
    }
});

const addTableOfContent = () => {
    tableOfContents.value.push({
        id: Date.now(),
        title: '',
        children: ['']
    });
};

const removeTableOfContent = (index) => {
    tableOfContents.value.splice(index, 1);
};

const addSubItem = (index) => {
    const item = tableOfContents.value[index];

    if (!item) {
        return;
    }

    if (!Array.isArray(item.children)) {
        item.children = [];
    }

    item.children.push('');
};

const removeSubItem = (parentIndex, childIndex) => {
    const item = tableOfContents.value[parentIndex];

    if (!item?.children) {
        return;
    }

    item.children.splice(childIndex, 1);
};

const addSection = () => {
    if (!contentEnabled.value) {
        warning('Please save Table of Content first.');

        return;
    }

    sections.value.push({
        id: Date.now(),
        type: 'text',
        title: '',
        content: '',
        image: null,
        imageFile: null
    });
};

const removeSection = (index) => {
    if (!contentEnabled.value) {
        return;
    }

    const section = sections.value[index];

    if (
        section?.image &&
        typeof section.image === 'string' &&
        section.image.startsWith('blob:')
    ) {
        URL.revokeObjectURL(section.image);
    }

    sections.value.splice(index, 1);
};

const moveSectionUp = (index) => {
    if (!contentEnabled.value || index <= 0) {
        return;
    }

    const current = sections.value[index];

    sections.value[index] = sections.value[index - 1];

    sections.value[index - 1] = current;
};

const moveSectionDown = (index) => {
    if (!contentEnabled.value || index >= sections.value.length - 1) {
        return;
    }

    const current = sections.value[index];

    sections.value[index] = sections.value[index + 1];

    sections.value[index + 1] = current;
};

const handleImageUpload = (event, section) => {
    if (!contentEnabled.value || !section) {
        return;
    }

    const file = event?.target?.files?.[0];

    event.target.value = '';

    if (!file) {
        return;
    }

    if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        return;
    }

    if (
        typeof section.image === 'string' &&
        section.image.startsWith('blob:')
    ) {
        URL.revokeObjectURL(section.image);
    }

    const index = sections.value.findIndex((item) => item.id === section.id);

    if (index === -1) {
        return;
    }

    const updatedSections = [...sections.value];

    updatedSections[index] = {
        ...updatedSections[index],
        imageFile: file,
        image: URL.createObjectURL(file)
    };

    sections.value = updatedSections;
};

const removeImage = (section) => {
    if (!contentEnabled.value) {
        return;
    }

    if (
        section.image &&
        typeof section.image === 'string' &&
        section.image.startsWith('blob:')
    ) {
        URL.revokeObjectURL(section.image);
    }

    section.image = null;
    section.imageFile = null;
};

const handleLogoUpload = (event) => {
    const file = event.target.files?.[0];

    event.target.value = '';

    if (!file) {
        return;
    }

    if (!file.type.startsWith('image/')) {
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        return;
    }

    if (caseStudy.value.logo && caseStudy.value.logo.startsWith('blob:')) {
        URL.revokeObjectURL(caseStudy.value.logo);
    }

    caseStudy.value.logo = URL.createObjectURL(file);

    caseStudy.value.logoFile = file;
};

const removeLogo = () => {
    if (caseStudy.value.logo && caseStudy.value.logo.startsWith('blob:')) {
        URL.revokeObjectURL(caseStudy.value.logo);
    }

    caseStudy.value.logo = '';
    caseStudy.value.logoFile = null;
};

const goBack = async () => {
    await router.push({
        name: 'case-studies.management',
        query: {
            tab: 'case-studies'
        }
    });
};

const saveTableOfContent = async (items) => {
    const id = String(route.params.id || '').trim();

    if (!id || isSavingTableOfContents.value) {
        return;
    }

    const normalizedTableOfContents = items.map((item, index) => ({
        id: item.id,

        order: index + 1,

        title: item.title?.trim() ?? '',

        children: Array.isArray(item.children)
            ? item.children.map((child) => child?.trim() ?? '').filter(Boolean)
            : []
    }));

    const hasValidContent = normalizedTableOfContents.some(
        (item) => item.title || item.children.length
    );

    if (!hasValidContent) {
        contentEnabled.value = false;

        warning('Please enter at least one Table of Content item.');

        return;
    }

    const hasEmptyTitle = normalizedTableOfContents.some((item) => !item.title);

    if (hasEmptyTitle) {
        contentEnabled.value = false;

        warning('Please enter a title for every Table of Content section.');

        return;
    }

    isSavingTableOfContents.value = true;

    try {
        await updateItem({
            id,
            tableOfContents: normalizedTableOfContents
        });

        tableOfContents.value = normalizedTableOfContents;

        syncSectionsWithTableOfContents();

        contentEnabled.value = true;

        success('Table of Content saved successfully.');
    } catch (err) {
        contentEnabled.value = false;

        error('Failed to save Table of Content. Please try again.');
    } finally {
        isSavingTableOfContents.value = false;
    }
};

const saveCaseStudy = async () => {
    const id = String(route.params.id || '').trim();

    if (!id || isSaving.value) {
        return;
    }

    isSaving.value = true;

    try {
        const images = [];

        const normalizedSections = sections.value.map((section) => {
            const result = {
                id: section.id,
                type: section.type ?? 'text',
                title: section.title?.trim() ?? '',
                content: section.content?.trim() ?? '',
                image: section.image ?? null
            };

            if (section.imageFile instanceof File) {
                const imageIndex = images.length;

                images.push(section.imageFile);

                result.image = {
                    url: '',
                    public_id: '',
                    _image_index: imageIndex
                };
            }

            return result;
        });

        let logo = caseStudy.value.logo || '';

        if (caseStudy.value.logoFile instanceof File) {
            const logoIndex = images.length;

            images.push(caseStudy.value.logoFile);

            logo = {
                url: '',
                public_id: '',
                _image_index: logoIndex
            };
        }

        const normalizedTableOfContents = tableOfContents.value.map(
            (item, index) => ({
                id: item.id,

                order: index + 1,

                title: item.title?.trim() ?? '',

                children: Array.isArray(item.children)
                    ? item.children
                          .map((child) => child?.trim() ?? '')
                          .filter(Boolean)
                    : []
            })
        );

        const payload = {
            id,

            title: caseStudy.value.title?.trim() ?? '',

            date: caseStudy.value.date ?? '',

            clientName: caseStudy.value.clientName?.trim() ?? '',

            author: caseStudy.value.author?.trim() ?? '',

            logo,

            planTitle: caseStudy.value.planTitle?.trim() ?? '',

            seriesTags: Array.isArray(caseStudy.value.seriesTags)
                ? caseStudy.value.seriesTags
                      .map((item) => String(item).trim())
                      .filter(Boolean)
                : [],

            categories: Array.isArray(caseStudy.value.categories)
                ? caseStudy.value.categories
                      .map((item) => String(item).trim())
                      .filter(Boolean)
                      .slice(0, 2)
                : [],

            description: caseStudy.value.description?.trim() ?? '',

            social_media: Array.isArray(caseStudy.value.social_media)
                ? caseStudy.value.social_media
                : [],

            tableOfContents: normalizedTableOfContents,

            sections: normalizedSections
        };

        await updateItem(payload, images);
    } catch (err) {
        error('Failed to save Case Study. Please try again.');
    } finally {
        isSaving.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen w-full bg-[#f5f7fb] px-6 py-6">
        <div class="w-full">
            <div
                class="mb-5 flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-5 py-3"
            >
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:bg-gray-50"
                        @click="goBack"
                    >
                        <ArrowLeft :size="17" />
                    </button>

                    <div>
                        <h1 class="text-[17px] font-semibold text-gray-800">
                            Case Study Detail
                        </h1>

                        <p class="mt-0.5 text-[11px] text-gray-400">
                            {{ caseStudyId }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="flex h-9 items-center gap-2 rounded-lg border border-gray-200 px-4 text-[12px] font-medium text-gray-600 transition hover:bg-gray-50"
                        @click="isPreview = !isPreview"
                    >
                        <Eye :size="15" />

                        {{ isPreview ? 'Edit' : 'Preview' }}
                    </button>
                </div>
            </div>
            <div
                v-if="loading"
                class="mb-5 w-full rounded-xl border border-gray-200 bg-white px-5 py-10 text-center"
            >
                <p class="text-sm text-gray-500">Loading case study...</p>
            </div>
            <div
                v-else-if="!caseStudy.id"
                class="mb-5 w-full rounded-xl border border-dashed border-gray-300 bg-white px-5 py-10 text-center"
            >
                <p class="text-sm text-gray-500">Case study not found.</p>
            </div>

            <template v-else>
                <CaseStudyPreview
                    v-if="isPreview"
                    :case-study="caseStudy"
                    :table-of-contents="tableOfContents"
                    :sections="sections"
                />

                <div
                    v-else
                    class="grid w-full grid-cols-1 items-start gap-5 xl:grid-cols-3"
                >
                    <CaseStudyInformation
                        v-model:case-study="caseStudy"
                        :series-options="seriesOptions"
                        :categories="availableCategories"
                        :saving="isSaving"
                        @upload-logo="handleLogoUpload"
                        @remove-logo="removeLogo"
                    />

                    <div class="min-w-0 space-y-5 xl:col-span-2">
                        <CaseStudyTableOfContents
                            v-model="tableOfContents"
                            :saving="isSavingTableOfContents"
                            @add="addTableOfContent"
                            @remove="removeTableOfContent"
                            @add-sub="addSubItem"
                            @remove-sub="removeSubItem"
                            @save="saveTableOfContent"
                        />

                        <CaseStudyContent
                            v-model="sections"
                            :saving="isSaving"
                            @add="addSection"
                            @remove="removeSection"
                            @move-up="moveSectionUp"
                            @move-down="moveSectionDown"
                            @upload-image="handleImageUpload"
                            @remove-image="removeImage"
                            @save="saveCaseStudy"
                        />
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
