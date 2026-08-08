<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Save, Plus, X, ImageIcon, Trash2 } from 'lucide-vue-next';

import ConfirmModal from '../../../components/common/ConfirmModal.vue';

import { useHomepage } from '../../../composables/home/useHomepage';

const { loading, getSection, saveItem, removeItem } = useHomepage();

const generalInformations = ref<any[]>([]);

const selectedId = ref<number | string | null>(null);

const showDeleteModal = ref(false);

const form = ref({
    id: null,
    isNew: false,
    badge: '',
    title: '',
    description: ''
});

const sectionData = getSection('generalInformation');

const selectedInformation = computed(() => {
    return generalInformations.value.find(
        (item) => item.id === selectedId.value
    );
});

const resetForm = () => {
    form.value = {
        id: null,

        isNew: false,

        badge: '',

        title: '',

        description: ''
    };
};

const selectInformation = (item) => {
    selectedId.value = item.id;

    const isTempItem = String(item.id ?? '').startsWith('temp-');

    form.value = {
        id: isTempItem ? null : item.id,
        badge: item.badge,
        title: item.title,
        description: item.description,
        isNew: isTempItem
    };
};

const loadData = () => {
    const data = sectionData.value;

    console.log('GENERAL DATA:', data);

    if (!data) return;

    console.log(
        'IDS:',
        (data.generalInformations ?? []).map((item: any) => item.id)
    );

    generalInformations.value = (data.generalInformations ?? []).map(
        (item: any) => ({
            id: item.id,
            badge: item.badge ?? '',
            title: item.title ?? '',
            description: item.description ?? ''
        })
    );

    if (!generalInformations.value.length) {
        selectedId.value = null;
        resetForm();
        return;
    }

    const activeItem =
        selectedId.value !== null
            ? generalInformations.value.find(
                  (item) => String(item.id) === String(selectedId.value)
              )
            : null;

    if (activeItem) {
        selectInformation(activeItem);
    } else {
        selectInformation(generalInformations.value[0]);
    }
};

const addInformation = () => {
    const tempItem = {
        id: `temp-${Date.now()}`,
        badge: '',
        title: '',
        description: ''
    };

    generalInformations.value.push(tempItem);
    selectedId.value = tempItem.id;

    form.value = {
        id: null,
        badge: '',
        title: '',
        description: '',
        isNew: true
    };
};

const createPayload = () => {
    return {
        badge: String(form.value.badge ?? '').trim(),
        title: String(form.value.title ?? '').trim(),
        description: String(form.value.description ?? '').trim()
    };
};

const handleSave = async () => {
    if (loading.value) return;

    const itemId = form.value.id ?? selectedId.value;
    const payload = createPayload();

    const isCreate =
        form.value.isNew ||
        !itemId ||
        String(itemId).startsWith('temp-');

    try {
        const result = isCreate
            ? await saveItem('generalInformation', 'generalInformations', payload)
            : await saveItem('generalInformation', 'generalInformations', {
                  id: itemId,
                  ...payload
              });

        const savedItem = result && typeof result === 'object' ? result : null;

        if (savedItem?.id) {
            selectedId.value = savedItem.id;
            form.value = {
                id: savedItem.id,
                badge: savedItem.badge ?? '',
                title: savedItem.title ?? '',
                description: savedItem.description ?? '',
                isNew: false
            };
        }

        await loadData();
    } catch (error) {
        console.error('Save general information failed:', error);
    }
};
const openDeleteModal = () => {
    if (!selectedId.value) return;

    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    showDeleteModal.value = false;

    if (!selectedId.value) return;

    const id = String(selectedId.value);

    console.log('DELETE ID:', id);

    if (id.startsWith('temp-')) {
        generalInformations.value = generalInformations.value.filter(
            (item) => item.id !== id
        );
    } else {
        const success = await removeItem(
            'generalInformation',
            'generalInformations',
            id
        );

        if (!success) {
            return;
        }

        await loadData();
    }

    selectedId.value = null;

    resetForm();
};

const cancelChanges = () => {
    if (selectedInformation.value) {
        selectInformation(selectedInformation.value);
    } else {
        resetForm();
    }
};

watch(
    sectionData,
    () => {
        loadData();
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
            <h2 class="text-2xl font-bold text-gray-900">
                General Information Management
            </h2>

            <p class="text-sm text-gray-500">
                Manage homepage general information.
            </p>
        </div>

        <div
            v-if="generalInformations.length"
            class="grid gap-6 xl:grid-cols-12"
        >
            <div class="xl:col-span-4">
                <div class="rounded-xl border bg-white p-4 shadow-sm">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-semibold">Information</h3>

                            <p class="text-sm text-gray-500">Select to edit</p>
                        </div>

                        <button
                            @click="addInformation"
                            class="rounded-lg bg-blue-600 p-2 text-white"
                        >
                            <Plus :size="18" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-2">
                        <button
                            v-for="item in generalInformations"
                            :key="item.id"
                            @click="selectInformation(item)"
                            class="w-full rounded-lg border p-3 text-left"
                            :class="
                                selectedId === item.id
                                    ? 'border-blue-600 bg-blue-50'
                                    : 'border-gray-200'
                            "
                        >
                            <p class="font-medium">
                                {{ item.title || 'Untitled' }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ item.badge || 'No badge' }}
                            </p>
                        </button>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-8">
                <div class="rounded-xl border bg-white p-5 shadow-sm">
                    <h3 class="text-lg font-semibold">
                        {{
                            form.isNew
                                ? 'Create General Information'
                                : 'Edit General Information'
                        }}
                    </h3>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label class="text-sm"> Badge </label>

                            <input
                                v-model="form.badge"
                                class="mt-1 w-full rounded-lg border px-3 py-2"
                            />
                        </div>

                        <div>
                            <label class="text-sm"> Title </label>

                            <input
                                v-model="form.title"
                                class="mt-1 w-full rounded-lg border px-3 py-2"
                            />
                        </div>

                        <div>
                            <label class="text-sm"> Description </label>

                            <textarea
                                v-model="form.description"
                                rows="5"
                                class="mt-1 w-full rounded-lg border px-3 py-2"
                            />
                        </div>

                        <div class="flex justify-end gap-3">
                            <button
                                @click="openDeleteModal"
                                class="rounded-lg border border-red-300 px-4 py-2 text-red-600"
                            >
                                <Trash2 :size="18" />
                            </button>

                            <button
                                type="button"
                                @click="handleSave"
                                :disabled="loading"
                                class="flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-white"
                            >
                                <Save :size="18" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else
            class="rounded-xl border border-dashed bg-white p-10 text-center"
        >
            <ImageIcon :size="32" class="mx-auto text-gray-400" />

            <h3 class="mt-3 font-semibold">No information found</h3>

            <button
                @click="addInformation"
                class="mt-5 rounded-lg bg-blue-600 px-5 py-2 text-white"
            >
                <Plus :size="18" />

                Add Information
            </button>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete General Information"
            message="Are you sure you want to delete this information?"
            @cancel="showDeleteModal = false"
            @confirm="confirmDelete"
        />
    </section>
</template>
