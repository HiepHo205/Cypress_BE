<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { Plus, Trash2, Save, ImageIcon } from 'lucide-vue-next';

import ConfirmModal from '../../../../components/common/ConfirmModal.vue';
import { useHomepage } from '../../../../composables/home/useHomepage.ts';

const { loading, getSection, saveSection, removeItem } = useHomepage();

const sections = ref<any[]>([]);

const selectedId = ref<string | number | null>(null);

const buttonSection = ref<any>(null);

const showDeleteModal = ref(false);

const deletingSection = ref<any>(null);

const sectionData = getSection('newsSections');

const selectedSection = computed(() => {
    return sections.value.find((item) => item.id === selectedId.value);
});

const loadData = () => {
    const data = sectionData.value;

    if (!data) return;

    sections.value = (data.sections ?? []).map((section: any) => ({
        id: section.id,

        key: section.key,

        title: section.title,

        buttonText: section.buttonText ?? '',

        buttonUrl: section.buttonUrl ?? '',

        items: section.items ?? []
    }));

    selectedId.value = sections.value[0]?.id ?? null;

    buttonSection.value = sections.value[0] ?? null;
};
const mode = ref('update');

const addSection = () => {
    const section = {
        id: `temp-${Date.now()}`,

        key: '',

        title: '',

        buttonText: '',

        buttonUrl: '',

        items: []
    };

    sections.value.push(section);

    selectedId.value = section.id;
};
const editSection = (section) => {
    mode.value = 'update';

    selectedId.value = section.id;
};
const addItem = () => {
    if (!selectedSection.value) return;

    const index = sections.value.findIndex(
        section => section.id === selectedSection.value.id
    );

    if (index === -1) return;

    sections.value[index] = {
        ...sections.value[index],
        items: [
            ...(sections.value[index].items ?? []),
            {
                id: `temp-${Date.now()}`,
                title: ''
            }
        ]
    };
};

const removeLocalItem = (item: any) => {
    if (!selectedSection.value) return;

    selectedSection.value.items = selectedSection.value.items.filter(
        (i: any) => i.id !== item.id
    );
};

const handleSave = async () => {
    await saveSection(
        'news_sections',
        {
            sections:
                mode.value === 'create'
                    ? [
                          ...sections.value,
                          {
                              ...selectedSection.value,
                              id: crypto.randomUUID()
                          }
                      ]
                    : sections.value.map((item) =>
                          item.id === selectedSection.value.id
                              ? selectedSection.value
                              : item
                      )
        },
        null,
        mode.value
    );

    mode.value = 'update';

    selectedSection.value = null;
};

const openDelete = () => {
    if (!selectedSection.value) return;

    deletingSection.value = selectedSection.value;

    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    showDeleteModal.value = false;

    if (!deletingSection.value) return;

    const id = deletingSection.value.id;

    if (String(id).startsWith('temp-')) {
        sections.value = sections.value.filter((item) => item.id !== id);
    } else {
        await removeItem('newsSections', 'sections', id); // Gọi API để xóa mục
        loadData(); // Tải lại dữ liệu từ backend để cập nhật UI
    }

    // Sau khi xóa, chọn mục đầu tiên còn lại hoặc null nếu không còn mục nào.
    selectedId.value = sections.value[0]?.id ?? null;

    deletingSection.value = null;
};

watch(
    sectionData,
    () => {
        loadData();
    },
    {
        immediate: true,
        deep: true
    }
);
</script>
<template>
    <section class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                News Sections Management
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Manage homepage news sections and items.
            </p>
        </div>

        <div
            v-if="buttonSection"
            class="rounded-3xl border bg-white p-6 shadow-sm"
        >
            <h3 class="text-lg font-semibold">Button Information</h3>

            <p class="mt-1 text-sm text-gray-500">Manage section button.</p>

            <div class="mt-5 grid grid-cols-2 gap-5">
                <div>
                    <label class="text-sm font-medium"> Button Text </label>

                    <input
                        v-model="buttonSection.buttonText"
                        placeholder="Learn More"
                        class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-600"
                    />
                </div>

                <div>
                    <label class="text-sm font-medium"> Button URL </label>

                    <input
                        v-model="buttonSection.buttonUrl"
                        placeholder="/contact"
                        class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-600"
                    />
                </div>
            </div>
        </div>

        <div v-if="sections.length" class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-4">
                <div class="rounded-3xl border bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold">Sections</h3>

                        <button
                            @click="addSection"
                            class="rounded-xl bg-blue-600 p-2 text-white"
                        >
                            <Plus :size="18" />
                        </button>
                    </div>

                    <div class="mt-5 space-y-3">
                        <button
                            v-for="section in sections"
                            :key="section.id"
                            @click="editSection(section)"
                            :class="[
                                'w-full rounded-xl border p-4 text-left transition',
                                selectedId === section.id
                                    ? 'border-blue-600 bg-blue-50'
                                    : 'border-gray-200'
                            ]"
                        >
                            <p class="font-semibold">
                                {{ section.title || 'Untitled' }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ section.items?.length || 0 }} items
                            </p>
                        </button>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-8">
                <div class="rounded-3xl border bg-white p-8 shadow-sm">
                    <div v-if="selectedSection">
                        <h3 class="text-xl font-semibold">
                            {{
                                mode === 'create'
                                    ? 'Create Section'
                                    : 'Edit Section'
                            }}
                        </h3>

                        <div class="mt-5">
                            <label class="text-sm font-medium"> Title </label>

                            <input
                                v-model="selectedSection.title"
                                class="mt-2 w-full rounded-xl border px-4 py-3"
                            />
                        </div>

                        <div class="mt-6">
                            <div class="flex justify-between">
                                <h4 class="font-semibold">Items</h4>

                                <button
                                    @click="addItem"
                                    class="flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-white"
                                >
                                    <Plus :size="16" />

                                    Add
                                </button>
                            </div>

                            <div class="mt-4 space-y-3">
                                <div
                                    v-for="(
                                        item, index
                                    ) in selectedSection.items"
                                    :key="item.id"
                                    class="flex gap-3"
                                >
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100"
                                    >
                                        {{ index + 1 }}
                                    </div>

                                    <input
                                        v-model="item.title"
                                        class="flex-1 rounded-xl border px-3"
                                    />

                                    <button
                                        @click="removeLocalItem(item)"
                                        class="text-red-500"
                                    >
                                        <Trash2 />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <button
                                @click="openDelete"
                                class="rounded-xl bg-red-100 p-3 text-red-600"
                            >
                                <Trash2 />
                            </button>

                            <button
                                @click="handleSave"
                                :disabled="loading"
                                class="rounded-xl bg-green-100 p-3 text-green-600"
                            >
                                <Save />
                            </button>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex h-80 items-center justify-center text-gray-400"
                    >
                        Select section
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="rounded-xl border border-dashed p-10 text-center">
            <ImageIcon class="mx-auto text-gray-400" />

            <h3 class="mt-3">No section found</h3>

            <button
                @click="addSection"
                class="mt-5 rounded-xl bg-blue-600 px-5 py-2 text-white"
            >
                Add Section
            </button>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Section"
            message="Are you sure?"
            @cancel="showDeleteModal = false"
            @confirm="confirmDelete"
        />
    </section>
</template>
