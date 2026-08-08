<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Plus, Save, Trash2, ImageIcon } from 'lucide-vue-next';

import ConfirmModal from '../../../../components/common/ConfirmModal.vue';
import { useHomepage } from '../../../../composables/home/useHomepage';

const { loading, getSection, saveSection, removeItem } = useHomepage();
const newsSection = getSection('news');
const mainNews = ref<any[]>([]);
const selectedId = ref<string | number | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const saving = ref(false);
const showDeleteModal = ref(false);
const selectedNews = computed(() => {
    return (
        mainNews.value.find(
            (item) => String(item.id) === String(selectedId.value)
        ) || null
    );
});
const loadMainNews = () => {
    const data = newsSection.value;

    if (!data) return;

    sectionForm.value = {
        label: data.label ?? '',
        title: data.title ?? ''
    };

    mainNews.value = (data.news ?? []).map((item: any) => ({
        id: item.id,
        category: item.category ?? '',
        date: item.date ?? '',
        title: item.title ?? '',
        description: item.description ?? '',
        image: item.image ?? null,
        imageFile: null,
        imagePreview: item.image?.url ?? ''
    }));

    selectedId.value = mainNews.value[0]?.id ?? null;
};

const selectNews = (item: any) => {
    selectedId.value = item.id;
};

const generateNewsId = (): string => {
    return `temp-${Date.now()}-${Math.random().toString(36).substring(2, 10)}`;
};

const addNews = () => {
    const newNews = {
        id: generateNewsId(),
        category: '',
        date: '',
        title: '',
        description: '',
        image: null,
        imageFile: null,
        imagePreview: null
    };

    mainNews.value.push(newNews);

    selectedId.value = newNews.id;
};

const openDeleteModal = () => {
    if (!selectedNews.value) return;
    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    showDeleteModal.value = false;

    const item = selectedNews.value;

    if (!item) return;

    try {
        if (String(item.id).startsWith('temp-')) {
            mainNews.value = mainNews.value.filter(
                (news) => news.id !== item.id
            );
        } else {
            await removeItem('news', 'news', String(item.id));
            await loadMainNews();
        }

        selectedId.value = mainNews.value[0]?.id ?? null;
    } catch (error) {
        console.error(error);
    }
};

const saveChanges = async () => {
    if (!selectedNews.value || saving.value) {
        return;
    }

    saving.value = true;

    try {
        const item = selectedNews.value;

        const input = {
            id: String(item.id).startsWith('temp-') ? null : String(item.id),
            category: item.category ?? '',
            date: item.date ?? '',
            title: item.title ?? '',
            description: item.description ?? ''
        };

        await saveSection(
            'news',
            {
                news: [input]
            },
            item.imageFile ?? null
        );

        await loadMainNews();
    } catch (error) {
        console.error('Save main news failed:', error);
    } finally {
        saving.value = false;
    }
};
const openFilePicker = () => {
    fileInput.value?.click();
};
const sectionForm = ref({
    label: '',
    title: ''
});
const handleUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file || !selectedNews.value) return;
    selectedNews.value.image = file;
    selectedNews.value.imageFile = file;
    selectedNews.value.imagePreview = URL.createObjectURL(file);
};

watch(
    newsSection,
    () => {
        loadMainNews();
    },
    {
        deep: true,
        immediate: true
    }
);
</script>
<template>
    <section class="space-y-4">
        <div>
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900">
                        Main News Information
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Configure the content displayed above the main news.
                    </p>
                </div>

                <div class="space-y-5 p-6">
                    <div>
                        <label class="mb-2 block text-sm font-medium">
                            Label
                        </label>

                        <input
                            v-model="sectionForm.label"
                            class="h-12 w-full rounded-xl border border-gray-200 px-4 focus:border-blue-600 focus:outline-none"
                            placeholder="Enter label"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium">
                            Title
                        </label>

                        <input
                            v-model="sectionForm.title"
                            class="h-12 w-full rounded-xl border border-gray-200 px-4 focus:border-blue-600 focus:outline-none"
                            placeholder="Enter title"
                        />
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="button"
                            @click="
                                saveSection('news', {
                                    label: sectionForm.label,
                                    title: sectionForm.title
                                })
                            "
                            :disabled="loading"
                            class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-white disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Save :size="17" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="mainNews.length || selectedNews"
            class="grid gap-6 xl:grid-cols-12"
        >
            <div class="xl:col-span-4">
                <div
                    class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Main News</h3>

                            <p class="text-sm text-gray-500">
                                Select news to edit
                            </p>
                        </div>

                        <button
                            @click="addNews"
                            class="flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-white"
                        >
                            <Plus :size="16" />

                            Add
                        </button>
                    </div>

                    <div class="mt-5 space-y-3">
                        <button
                            v-for="item in mainNews"
                            :key="item.id"
                            @click="selectNews(item)"
                            class="flex w-full gap-3 rounded-xl border p-3 text-left"
                            :class="
                                selectedId === item.id
                                    ? 'border-blue-600 bg-blue-50'
                                    : 'border-gray-200'
                            "
                        >
                            <div>
                                <img
                                    v-if="item.imagePreview"
                                    :src="item.imagePreview"
                                    class="h-14 w-16 rounded-lg object-cover"
                                />

                                <div
                                    v-else
                                    class="flex h-14 w-16 items-center justify-center rounded-lg bg-gray-100"
                                >
                                    <ImageIcon :size="22" />
                                </div>
                            </div>

                            <div class="min-w-0">
                                <p class="text-xs text-blue-600">
                                    {{ item.category }}
                                </p>

                                <p class="truncate font-semibold">
                                    {{ item.title || 'Untitled' }}
                                </p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <div class="xl:col-span-8">
                <div class="rounded-2xl border bg-white p-6 shadow-sm">
                    <div v-if="selectedNews">
                        <h3 class="text-xl font-semibold">Edit Main News</h3>

                        <div class="mt-6 grid grid-cols-5 gap-6">
                            <div class="col-span-2">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleUpload"
                                />

                                <div
                                    @click="openFilePicker"
                                    class="flex aspect-square cursor-pointer items-center justify-center overflow-hidden rounded-xl border"
                                >
                                    <img
                                        v-if="selectedNews.imagePreview"
                                        :src="selectedNews.imagePreview"
                                        class="h-full w-full object-cover"
                                    />

                                    <ImageIcon v-else :size="40" />
                                </div>
                            </div>

                            <div class="col-span-3 space-y-4">
                                <input
                                    v-model="selectedNews.category"
                                    placeholder="Category"
                                    class="w-full rounded-lg border px-3 py-2"
                                />

                                <input
                                    v-model="selectedNews.date"
                                    type="date"
                                    class="w-full rounded-lg border px-3 py-2"
                                />

                                <input
                                    v-model="selectedNews.title"
                                    placeholder="Title"
                                    class="w-full rounded-lg border px-3 py-2"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            @click="openDeleteModal"
                            class="rounded-lg border border-red-300 px-4 py-2 text-red-600"
                        >
                            <Trash2 :size="18" />
                        </button>

                        <button
                            @click="saveChanges"
                            :disabled="saving"
                            class="flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-white"
                        >
                            <Save :size="18" />

                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else
            class="rounded-xl border border-dashed bg-white p-10 text-center"
        >
            <ImageIcon class="mx-auto" />

            <h3 class="mt-3 font-semibold">No main news found</h3>

            <button
                @click="addNews"
                class="mt-5 rounded-lg bg-blue-600 px-5 py-2 text-white"
            >
                <Plus :size="18" />

                Add News
            </button>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Main News"
            message="Are you sure you want to delete this news?"
            @cancel="showDeleteModal = false"
            @confirm="confirmDelete"
        />
    </section>
</template>
