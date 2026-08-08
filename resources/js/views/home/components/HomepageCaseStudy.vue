<script setup>
import { onMounted, ref, watch } from 'vue';
import { ImageIcon, Plus, Save, Trash2 } from 'lucide-vue-next';

import { useHomepage } from '../../../composables/home/useHomepage';

import ConfirmModal from '../../../components/common/ConfirmModal.vue';

const { loading, getSection, saveItem, removeItem } = useHomepage();

const caseStudies = ref([]);

const selectedId = ref(null);

const selectedCaseStudy = ref(null);

const fileInput = ref(null);

const logoInput = ref(null);

const showDeleteModal = ref(false);

const caseStudySection = getSection('caseStudies');

const normalizeCaseStudy = (item) => ({
    id: item.id ?? `temp-${Date.now()}`,
    title: item.title ?? '',
    summary: item.summary ?? '',
    planTitle: item.planTitle ?? '',
    seriesTags: item.seriesTags ?? '',
    learnMoreText: item.learnMoreText ?? 'Learn more',
    learnMoreUrl: item.learnMoreUrl ?? '',
    active: item.active ?? true,
    logo: item.logo ?? null,
    logoPreview: item.logo?.url ?? null, // Khởi tạo với URL logo hiện có
    logoFile: null,
    image: item.image ?? null,
    imagePreview: item.image?.url ?? null, // Khởi tạo với URL ảnh hiện có
    imageFile: null
});

const load = () => {
    const data = caseStudySection.value;

    if (!data) {
        caseStudies.value = [];
        selectedCaseStudy.value = null;
        selectedId.value = null;
        return;
    }

    const items = Array.isArray(data.caseStudies) ? data.caseStudies : [];

    caseStudies.value = items.map(normalizeCaseStudy);

    if (caseStudies.value.length > 0) {
        const currentItem = caseStudies.value.find(
            (item) => item.id === selectedId.value
        );

        selectedCaseStudy.value = currentItem ?? caseStudies.value[0];

        selectedId.value = selectedCaseStudy.value.id;
    } else {
        selectedCaseStudy.value = null;
        selectedId.value = null;
    }
};

watch(
    caseStudySection,
    () => {
        load();
    },
    {
        deep: true,
        immediate: true
    }
);
const selectCaseStudy = (item) => {
    selectedCaseStudy.value = {
        ...item,

        logoPreview: item.logo?.url ?? null,
        logoFile: null,

        imagePreview: item.image?.url ?? null,
        imageFile: null
    };

    selectedId.value = item.id;
};
const addCaseStudy = () => {
    const item = {
        id: crypto.randomUUID(),

        label: '',
        title: '',
        summary: '',
        planTitle: '',
        seriesTags: '',
        learnMoreText: 'Learn more',
        learnMoreUrl: '',

        logo: null,
        logoPreview: null,
        logoFile: null,

        image: null,
        imagePreview: null,
        imageFile: null,

        active: true
    };

    caseStudies.value.push(item);

    selectedCaseStudy.value = item;
    selectedId.value = item.id;
};
const openImagePicker = () => {
    fileInput.value?.click();
};

const openLogoPicker = () => {
    logoInput.value?.click();
};

const handleImageChange = (event) => {
    const file = event.target.files?.[0];

    if (!file) return;

    selectedCaseStudy.value.imageFile = file;
    selectedCaseStudy.value.imagePreview = URL.createObjectURL(file);
};

const handleLogoChange = (event) => {
    const file = event.target.files?.[0];

    if (!file || !selectedCaseStudy.value) {
        return;
    }

    if (selectedCaseStudy.value.logoPreview) {
        URL.revokeObjectURL(selectedCaseStudy.value.logoPreview);
    }

    selectedCaseStudy.value.logoFile = file;

    selectedCaseStudy.value.logoPreview = URL.createObjectURL(file);

    selectedCaseStudy.value.logo = null;
};

const save = async () => {
    try {
        loading.value = true;

        const response = await saveItem(
            'caseStudies',
            'caseStudies',
            {
                id: selectedCaseStudy.value.id,
                title: selectedCaseStudy.value.title,
                summary: selectedCaseStudy.value.summary,
                planTitle: selectedCaseStudy.value.planTitle,
                seriesTags: selectedCaseStudy.value.seriesTags,
                learnMoreText: selectedCaseStudy.value.learnMoreText,
                learnMoreUrl: selectedCaseStudy.value.learnMoreUrl,
                active: selectedCaseStudy.value.active
            },
            {
                image: selectedCaseStudy.value.imageFile,
                logo: selectedCaseStudy.value.logoFile
            },
            'homepage/case-studies'
        );
        const updatedItem = JSON.parse(response.data.updateHomepageItem);

        selectedCaseStudy.value = {
            ...updatedItem,

            logoPreview: null,
            logoFile: null,

            imagePreview: null,
            imageFile: null
        };

        const index = caseStudies.value.findIndex(
            (item) => item.id === updatedItem.id
        );

        if (index !== -1) {
            caseStudies.value[index] = {
                ...updatedItem,

                logoPreview: null,
                logoFile: null,

                imagePreview: null,
                imageFile: null
            };
        }

        toast.success('Saved successfully');
    } finally {
        loading.value = false;
    }
};
const openDeleteModal = () => {
    if (!selectedCaseStudy.value) {
        return;
    }

    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    if (!selectedCaseStudy.value) {
        return;
    }

    const id = selectedCaseStudy.value.id;

    if (String(id).startsWith('temp-')) {
        caseStudies.value = caseStudies.value.filter((item) => item.id !== id);

        selectedCaseStudy.value = caseStudies.value[0] ?? null;

        selectedId.value = selectedCaseStudy.value?.id ?? null;

        showDeleteModal.value = false;

        return;
    }

    await removeItem('caseStudies', 'caseStudies', id);

    showDeleteModal.value = false;

    selectedCaseStudy.value = null;

    selectedId.value = null;

    await load();
};

onMounted(() => {
    load();
});
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Case Study Management
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Manage homepage case studies.
            </p>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-4 rounded-xl border bg-white p-4 shadow-sm">
                <div class="mb-5 flex justify-between">
                    <h3 class="font-semibold">Case Studies</h3>

                    <button
                        type="button"
                        @click="addCaseStudy"
                        class="rounded-lg bg-blue-600 p-2 text-white"
                    >
                        <Plus :size="18" />
                    </button>
                </div>

                <div class="space-y-3">
                    <button
                        v-for="item in caseStudies"
                        :key="item.id"
                        @click.stop="selectCaseStudy(item)"
                        class="flex w-full items-center gap-3 rounded-xl border p-3 text-left"
                        :class="
                            selectedId === item.id
                                ? 'border-blue-600 bg-blue-50'
                                : 'border-gray-200'
                        "
                    >
                        <img
                            v-if="item.imagePreview || item.image?.url"
                            :src="item.imagePreview || item.image?.url"
                            class="h-14 w-14 rounded-lg object-cover"
                        />

                        <div
                            v-else
                            class="flex h-14 w-14 items-center justify-center rounded-lg bg-gray-100"
                        >
                            <ImageIcon :size="18" />
                        </div>

                        <div>
                            <p class="font-medium">
                                {{ item.title || 'Untitled' }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ item.label }}
                            </p>
                        </div>
                    </button>
                </div>
            </div>

            <div class="col-span-8 rounded-xl border bg-white p-5 shadow-sm">
                <div v-if="selectedCaseStudy" class="space-y-5">
                    <div>
                        <h3 class="text-lg font-semibold">
                            Case Study Information
                        </h3>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Label
                        </label>

                        <input
                            v-model="selectedCaseStudy.label"
                            placeholder="Enter case study label"
                            class="w-full rounded-xl border px-4 py-3"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Title
                        </label>

                        <input
                            v-model="selectedCaseStudy.title"
                            placeholder="Enter title"
                            class="w-full rounded-xl border px-4 py-3"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Summary
                        </label>

                        <textarea
                            v-model="selectedCaseStudy.summary"
                            rows="5"
                            placeholder="Enter summary"
                            class="w-full rounded-xl border px-4 py-3"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Plan Title
                            </label>

                            <input
                                v-model="selectedCaseStudy.planTitle"
                                placeholder="Enter plan title"
                                class="w-full rounded-xl border px-4 py-3"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Series Tags
                            </label>

                            <input
                                v-model="selectedCaseStudy.seriesTags"
                                placeholder="Marketing, AI Agent..."
                                class="rounded-xl border px-4 py-3"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Learn More Text
                            </label>

                            <input
                                v-model="selectedCaseStudy.learnMoreText"
                                placeholder="Learn more"
                                class="rounded-xl border px-4 py-3"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Learn More URL
                            </label>

                            <input
                                v-model="selectedCaseStudy.learnMoreUrl"
                                placeholder="/learn-more"
                                class="rounded-xl border px-4 py-3"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Logo Image
                            </label>

                            <input
                                ref="logoInput"
                                type="file"
                                class="hidden"
                                @change="handleLogoChange"
                            />

                            <button
                                type="button"
                                @click="openLogoPicker"
                                class="flex h-28 w-full items-center justify-center rounded-xl border-2 border-dashed"
                            >
                                <img
                                    v-if="
                                        selectedCaseStudy.logoPreview ||
                                        selectedCaseStudy.logo?.url
                                    "
                                    :src="
                                        selectedCaseStudy.logoPreview ||
                                        selectedCaseStudy.logo?.url
                                    "
                                    class="h-full w-full object-contain"
                                />

                                <ImageIcon v-else />
                            </button>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Case Study Image
                            </label>

                            <input
                                ref="fileInput"
                                type="file"
                                class="hidden"
                                @change="handleImageChange"
                            />

                            <button
                                @click="openImagePicker"
                                class="flex h-40 w-full items-center justify-center rounded-xl border-2 border-dashed"
                            >
                                <img
                                    v-if="
                                        selectedCaseStudy.imagePreview ||
                                        selectedCaseStudy.image?.url
                                    "
                                    :src="
                                        selectedCaseStudy.imagePreview ||
                                        selectedCaseStudy.image?.url
                                    "
                                    class="h-full w-full object-cover"
                                />

                                <ImageIcon v-else />
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-5">
                        <button
                            type="button"
                            @click="openDeleteModal"
                            class="rounded-xl border border-red-200 px-5 py-2 text-red-600"
                        >
                            <Trash2 :size="18" />
                        </button>

                        <button
                            type="button"
                            @click="save"
                            :disabled="loading"
                            class="rounded-xl bg-blue-600 px-6 py-2 text-white"
                        >
                            <Save :size="18" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Case Study"
            message="Are you sure you want to delete this case study?"
            @confirm="confirmDelete"
            @cancel="showDeleteModal = false"
        />
    </section>
</template>
