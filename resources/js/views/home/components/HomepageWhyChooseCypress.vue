<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { Plus, Save, Trash2, ImageIcon } from 'lucide-vue-next';
import { useHomepage } from '../../../composables/home/useHomepage';
import ConfirmModal from '../../../components/common/ConfirmModal.vue';

const { loading, getSection, saveSection, saveItem, removeItem } =
    useHomepage();

const whyChoose = getSection('whyChooseCypress');
const isCreating = ref(false);
const sectionForm = ref({
    badge: '',
    title: '',
    description: ''
});

const benefits = ref([]);
const selectedBenefit = ref<any>(null);
const selectedId = ref<string | null>(null);

const showDeleteModal = ref(false);

const populateForm = (value: any) => {
    if (!value) {
        return;
    }

    sectionForm.value = {
        badge: value.badge || '',
        title: value.title || '',
        description: value.description || ''
    };

    benefits.value = Array.isArray(value.benefits)
        ? JSON.parse(JSON.stringify(value.benefits))
        : [];

    if (!selectedBenefit.value && benefits.value.length) {
        selectedBenefit.value = benefits.value[0];
        selectedId.value = benefits.value[0].id ?? null;
    }
};
watch(
    () => whyChoose.value,
    (value) => {
        if (value) {
            populateForm(value);
        }
    },
    {
        immediate: true
    }
);
onMounted(() => {
    populateForm(whyChoose.value);
});
const selectBenefit = (item: any) => {
    selectedBenefit.value = item;
    selectedId.value = item.id;
};

const addBenefit = () => {
    const item = {
        id: `temp-${Date.now()}`,
        title: '',
        description: '',
        icon: null,
        active: true
    };

    benefits.value.push(item);

    selectedBenefit.value = item;
};

const saveInformation = async () => {
    await saveSection('whyChooseCypress', {
        badge: sectionForm.value.badge,
        title: sectionForm.value.title,
        description: sectionForm.value.description
    });
};

const save = async () => {
    if (!selectedBenefit.value) {
        return;
    }

    await saveItem('whyChooseCypress', 'benefits', {
        id: selectedBenefit.value.id,
        title: selectedBenefit.value.title,
        description: selectedBenefit.value.description,
        active: true
    });
};

const removeBenefit = async () => {
    if (!selectedBenefit.value?.id) {
        return;
    }

    await removeItem('whyChooseCypress', 'benefits', selectedBenefit.value.id);

    showDeleteModal.value = false;
    selectedBenefit.value = null;
};

const openDeleteModal = () => {
    showDeleteModal.value = true;
};

const confirmDeleteBenefit = () => {
    removeBenefit();
};
const iconInput = ref<HTMLInputElement | null>(null);

const openIconPicker = () => {
    iconInput.value?.click();
};

const handleIconUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file || !selectedBenefit.value) {
        return;
    }

    const url = URL.createObjectURL(file);

    selectedBenefit.value.icon = {
        url,
        file,
    };

    if (iconInput.value) {
        iconInput.value.value = '';
    }
};
</script>

<template>
    <section class="space-y-6">
        <div class="rounded-xl border bg-white">
            <div class="border-b p-6">
                <h2 class="font-semibold">General Information</h2>

                <p class="mt-1 text-sm text-gray-500">
                    Configure the content displayed above the benefits.
                </p>
            </div>

            <div class="space-y-5 p-6">
                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Badge
                    </label>

                    <input
                        v-model="sectionForm.badge"
                        class="h-12 w-full rounded-xl border px-4"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Title
                    </label>

                    <input
                        v-model="sectionForm.title"
                        class="h-12 w-full rounded-xl border px-4"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Description
                    </label>

                    <textarea
                        v-model="sectionForm.description"
                        rows="4"
                        class="w-full rounded-xl border px-4 py-3"
                    />
                </div>

                <div class="flex justify-end">
                    <button
                        type="button"
                        @click="saveInformation"
                        :disabled="loading"
                        class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-white disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <Save :size="17" />
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-4 rounded-xl border bg-white p-4">
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="font-semibold">Benefits</h3>

                    <button
                        type="button"
                        @click="addBenefit"
                        class="rounded-lg bg-blue-600 p-2 text-white"
                    >
                        <Plus :size="18" />
                    </button>
                </div>

                <div class="space-y-3">
                    <button
                        v-for="(item, index) in benefits"
                        :key="item.id || index"
                        type="button"
                        @click="selectBenefit(item)"
                        class="flex w-full gap-3 rounded-xl border p-3 text-left"
                    >
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gray-100"
                        >
                            <img
                                v-if="item.icon?.url"
                                :src="item.icon.url"
                                alt="Benefit icon"
                                class="h-full w-full object-contain"
                            />

                            <ImageIcon
                                v-else
                                :size="20"
                                class="text-gray-400"
                            />
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-medium">
                                {{ item.title || 'Untitled' }}
                            </p>

                            <p class="mt-1 line-clamp-2 text-xs text-gray-500">
                                {{ item.description || 'No description' }}
                            </p>
                        </div>
                    </button>
                </div>
            </div>

            <div class="col-span-8 rounded-xl border bg-white p-5">
                <div v-if="selectedBenefit" class="space-y-5">
                    <div class="border-b pb-4">
                        <h2 class="font-semibold">
                            {{
                                isCreating
                                    ? 'Create Benefit'
                                    : `Edit Benefit ${selectedBenefit.title || ''}`
                            }}
                        </h2>
                    </div>

                    <div class="flex items-end gap-5">
                        <div class="shrink-0">
                            <label class="mb-2 block text-sm font-medium">
                                Icon
                            </label>

                            <input
                                ref="iconInput"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleIconUpload"
                            />

                            <button
                                type="button"
                                @click="openIconPicker"
                                :disabled="loading"
                                class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-xl border bg-gray-50 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <img
                                    v-if="selectedBenefit.icon?.url"
                                    :src="selectedBenefit.icon.url"
                                    alt="Benefit icon"
                                    class="h-full w-full object-contain"
                                />

                                <ImageIcon
                                    v-else
                                    :size="28"
                                    class="text-gray-400"
                                />
                            </button>
                        </div>

                        <div class="flex-1">
                            <label class="mb-2 block text-sm font-medium">
                                Title
                            </label>

                            <input
                                v-model="selectedBenefit.title"
                                class="h-12 w-full rounded-xl border px-4"
                                placeholder="Enter benefit title"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium">
                            Description
                        </label>

                        <textarea
                            v-model="selectedBenefit.description"
                            rows="6"
                            class="w-full rounded-xl border px-4 py-3"
                            placeholder="Enter benefit description"
                        />
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="openDeleteModal"
                            class="rounded-xl border px-5 py-2 text-red-600 hover:bg-red-50"
                        >
                            <Trash2 :size="18" />
                        </button>

                        <button
                            type="button"
                            @click="save"
                            :disabled="loading"
                            class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2 text-white disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Save :size="18" />
                            Save
                        </button>
                    </div>
                </div>

                <div
                    v-else
                    class="flex min-h-[400px] items-center justify-center text-center"
                >
                    <div>
                        <ImageIcon :size="40" class="mx-auto text-gray-300" />

                        <h3 class="mt-3 font-semibold text-gray-700">
                            No Benefit Selected
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Select a benefit or create a new one.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Benefit"
            message="Are you sure you want to delete this benefit?"
            @confirm="confirmDeleteBenefit"
            @cancel="showDeleteModal = false"
        />
    </section>
</template>
