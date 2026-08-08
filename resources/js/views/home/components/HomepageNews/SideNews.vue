<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { ImageIcon, Plus, Save, Trash2 } from 'lucide-vue-next';

import ConfirmModal from '../../../../components/common/ConfirmModal.vue';
import { useHomepage } from '../../../../composables/home/useHomepage';

const { loading, getSection, saveSection, removeItem } = useHomepage();

const sideNews = ref<any[]>([]);

const selectedId = ref<string | number | null>(null);

const fileInput = ref<HTMLInputElement | null>(null);

const saving = ref(false);

const showDeleteModal = ref(false);

const sectionData = getSection('sideNews');

const selectedNews = computed(() => {
    return sideNews.value.find((item) => item.id === selectedId.value);
});

const loadData = () => {
    const data = sectionData.value;

    if (!data) return;

    sideNews.value = (data.sideNews ?? []).map((item: any) => ({
        id: item.id,

        description: item.description ?? '',

        image: item.image,

        imageFile: null,

        imagePreview: item.image?.url ?? ''
    }));

    selectedId.value = sideNews.value[0]?.id ?? null;
};

const addNews = () => {
    const item = {
        id: `temp-${Date.now()}`,

        description: '',

        image: null,

        imageFile: null,

        imagePreview: ''
    };

    sideNews.value.push(item);

    selectedId.value = item.id;
};

const openDeleteModal = () => {
    if (!selectedNews.value) return;

    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    showDeleteModal.value = false;

    const item = selectedNews.value;

    if (!item) return;

    if (String(item.id).startsWith('temp-')) {
        sideNews.value = sideNews.value.filter((x) => x.id !== item.id);
    } else {
        await removeItem('sideNews', 'sideNews', item.id);
    }

    selectedId.value = sideNews.value[0]?.id ?? null;
};

const saveChanges = async () => {
    const item = selectedNews.value;

    if (!item) return;

    saving.value = true;

    try {
        await saveSection(
            'sideNews',

            {
                items: [
                    {
                        id: String(item.id).startsWith('temp-')
                            ? null
                            : item.id,

                        description: item.description
                    }
                ]
            },

            item.imageFile ?? null
        );

        loadData();
    } finally {
        saving.value = false;
    }
};

const openFilePicker = () => {
    fileInput.value?.click();
};

const handleUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;

    const file = target.files?.[0];

    if (!file || !selectedNews.value) return;

    selectedNews.value.imageFile = file;

    selectedNews.value.imagePreview = URL.createObjectURL(file);
};

onMounted(() => {
    loadData();
});
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Side News Management
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Manage side news displayed on homepage.
            </p>
        </div>

        <div v-if="sideNews.length" class="grid gap-6 xl:grid-cols-12">
            <!-- LEFT LIST -->

            <div class="xl:col-span-4">
                <div class="rounded-xl border bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold">Side News</h3>

                            <p class="text-sm text-gray-500">
                                Select item to edit
                            </p>
                        </div>

                        <button
                            @click="addNews"
                            class="rounded-lg bg-blue-600 p-2 text-white"
                        >
                            <Plus :size="18" />
                        </button>
                    </div>

                    <div class="mt-5 space-y-3">
                        <button
                            v-for="item in sideNews"
                            :key="item.id"
                            @click="selectedId = item.id"
                            class="flex w-full gap-3 rounded-xl border p-3 text-left"
                            :class="
                                selectedId === item.id
                                    ? 'border-blue-600 bg-blue-50'
                                    : 'border-gray-200'
                            "
                        >
                            <img
                                v-if="item.imagePreview"
                                :src="item.imagePreview"
                                class="h-16 w-16 rounded-lg object-cover"
                            />

                            <div
                                v-else
                                class="flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100"
                            >
                                <ImageIcon :size="24" class="text-gray-400" />
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">
                                    {{ item.description || 'No description' }}
                                </p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-8">
                <div class="rounded-xl border bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold">
                        {{
                            selectedNews?.id
                                ? 'Edit Side News'
                                : 'Create Side News'
                        }}
                    </h3>

                    <div
                        v-if="selectedNews"
                        class="mt-6 grid gap-6 lg:grid-cols-3"
                    >
                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                Image
                            </label>

                            <input
                                ref="fileInput"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleUpload"
                            />

                            <div
                                @click="openFilePicker"
                                class="flex h-28 w-28 cursor-pointer items-center justify-center overflow-hidden rounded-xl border border-dashed"
                            >
                                <img
                                    v-if="selectedNews.imagePreview"
                                    :src="selectedNews.imagePreview"
                                    class="h-full w-full object-cover"
                                />

                                <ImageIcon
                                    v-else
                                    :size="32"
                                    class="text-gray-400"
                                />
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="mb-2 block text-sm font-medium">
                                Description
                            </label>

                            <textarea
                                v-model="selectedNews.description"
                                rows="6"
                                class="w-full rounded-xl border px-3 py-2"
                                placeholder="Enter description"
                            />
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
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
            <ImageIcon :size="32" class="mx-auto text-gray-400" />

            <h3 class="mt-3 font-semibold">No side news found</h3>

            <button
                @click="addNews"
                class="mt-5 rounded-lg bg-blue-600 px-5 py-2 text-white"
            >
                <Plus :size="18" />

                Add Side News
            </button>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Side News"
            message="Are you sure you want to delete this side news?"
            @cancel="showDeleteModal = false"
            @confirm="confirmDelete"
        />
    </section>
</template>
