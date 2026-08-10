<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { Save, Upload, Trash2, Plus, X } from 'lucide-vue-next';
import { useHomepage } from '../../../composables/home/useHomepage';
const { loading, getSection, saveSection, removeItem } = useHomepage();
const contact = ref<any>({
    title: '',
    description: '',
    termsText: '',
    termsLabel: '',
    termsUrl: '',
    buttonText: '',
    buttonUrl: '',
    image: null,
    labels: []
});
const previewImage = ref<string | null>(null);
const selectedLabelId = ref<string | number | null>(null);
const contactSection = getSection('contact');
const selectedLabel = computed(() => {
    if (!selectedLabelId.value) {
        return null;
    }
    return (
        contact.value.labels.find(
            (item: any) => String(item.id) === String(selectedLabelId.value)
        ) || null
    );
});

const loadData = () => {
    const data = contactSection.value;
    if (!data) return;
    data.labels?.forEach((item: any) => {
    });
    contact.value = {
        title: data.title ?? '',
        description: data.description ?? '',
        termsText: data.termsText ?? '',
        termsLabel: data.termsLabel ?? '',
        termsUrl: data.termsUrl ?? '',
        buttonText: data.buttonText ?? '',
        buttonUrl: data.buttonUrl ?? '',
        linkButtonText: data.linkButtonText ?? '',
        linkButtonUrl: data.linkButtonUrl ?? '',
        image: data.image ?? null,
        labels: (data.labels ?? []).map((item: any) => ({
            id: item.id,
            title: item.title ?? '',
            placeholder: item.placeholder ?? '',
            type: item.type ?? 'input',
            options: Array.isArray(item.options)
                ? item.options.map((opt: any) => ({
                      id: opt.id ?? null,
                      value: opt.value ?? ''
                  }))
                : []
        }))
    };
    previewImage.value = data.image?.url ?? null;
    selectedLabelId.value = contact.value.labels[0]?.id ?? null;
};
watch(
    contactSection,
    () => {
        loadData();
    },
    {
        deep: true,
        immediate: true
    }
);

const addLabel = () => {
    const item = {
        id: `temp-${Date.now()}`,
        title: '',
        placeholder: '',
        type: 'input',
        options: []
    };
    contact.value.labels = [...contact.value.labels, item];
    selectedLabelId.value = item.id;
};

const removeLabel = async (id: any) => {
    const label = contact.value.labels.find((item: any) => item.id === id);
    if (!label) return;
    if (String(id).startsWith('temp-')) {
        contact.value.labels = contact.value.labels.filter(
            (item: any) => item.id !== id
        );
        selectedLabelId.value = contact.value.labels[0]?.id ?? null;
        return;
    }
    try {
        await removeItem('contact', 'labels', String(id));
        contact.value.labels = contact.value.labels.filter(
            (item: any) => item.id !== id
        );
        selectedLabelId.value = contact.value.labels[0]?.id ?? null;
    } catch (error) {
        console.error('Delete label error:', error);
    }
};

const uploadImage = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;
    contact.value.image = file;
    previewImage.value = URL.createObjectURL(file);
};

const save = async () => {
    await saveSection(
        'contact',
        {
            title: contact.value.title,
            description: contact.value.description,
            termsText: contact.value.termsText,
            termsLabel: contact.value.termsLabel,
            termsUrl: contact.value.termsUrl,
            buttonText: contact.value.buttonText,
            buttonUrl: contact.value.buttonUrl,
            labels: contact.value.labels.map((item: any) => ({
                id: String(item.id).startsWith('temp') ? null : item.id,
                title: item.title,
                placeholder: item.placeholder,
                type: item.type,

                options:
                    item.options?.map((option: any) => ({
                        id: option.id,
                        value: option.value
                    })) ?? []
            }))
        },

        contact.value.image instanceof File ? contact.value.image : null
    );
};
const addOption = () => {
    if (!selectedLabel.value) return;
    if (!Array.isArray(selectedLabel.value.options)) {
        selectedLabel.value.options = [];
    }
    selectedLabel.value.options.push({
        id: null,
        value: ''
    });
};

const removeOption = (index: number) => {
    if (!selectedLabel.value) return;
    selectedLabel.value.options = [
        ...selectedLabel.value.options.slice(0, index),
        ...selectedLabel.value.options.slice(index + 1)
    ];
};
</script>
<template>
    <section class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Contact Management</h2>
            <p class="mt-2 text-sm text-gray-500">
                Manage contact information displayed on homepage.
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h3 class="mb-5 text-lg font-semibold">Contact Information</h3>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Title
                    </label>

                    <input
                        v-model="contact.title"
                        placeholder="Enter title"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 outline-none focus:border-blue-600"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Terms Text
                    </label>
                    <input
                        v-model="contact.termsText"
                        placeholder="I agree with"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 outline-none focus:border-blue-600"
                    />
                </div>

                <div class="col-span-2">
                    <label class="mb-2 block text-sm font-medium">
                        Description
                    </label>

                    <textarea
                        v-model="contact.description"
                        rows="5"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 outline-none focus:border-blue-600"
                        placeholder="Enter description"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Terms Label
                    </label>

                    <input
                        v-model="contact.termsLabel"
                        placeholder="Terms of Service"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 outline-none focus:border-blue-600"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Terms URL
                    </label>

                    <input
                        v-model="contact.termsUrl"
                        placeholder="/terms"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 outline-none focus:border-blue-600"
                    />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Button Text
                    </label>

                    <input
                        v-model="contact.buttonText"
                        placeholder="Get Started"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 outline-none focus:border-blue-600"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Button URL
                    </label>

                    <input
                        v-model="contact.buttonUrl"
                        placeholder="/contact"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 outline-none focus:border-blue-600"
                    />
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 gap-6">
            <div
                class="col-span-4 space-y-6 rounded-xl border border-gray-200 bg-white p-5"
            >
                <div>
                    <h3 class="mb-4 text-lg font-semibold">Contact Image</h3>

                    <label class="block w-fit cursor-pointer">
                        <div
                            v-if="previewImage"
                            class="relative h-32 w-32 overflow-hidden rounded-xl border border-gray-100"
                        >
                            <img
                                :src="previewImage"
                                class="h-full w-full object-cover"
                            />
                        </div>

                        <div
                            v-else
                            class="flex h-32 w-32 flex-col items-center justify-center rounded-xl border border-dashed bg-gray-50"
                        >
                            <Upload :size="24" class="text-gray-400" />

                            <span class="mt-1 text-xs text-gray-500">
                                Upload
                            </span>
                        </div>

                        <input
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="uploadImage"
                        />
                    </label>
                </div>
                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold">Labels</h3>

                            <p class="text-sm text-gray-500">Select label</p>
                        </div>

                        <button
                            @click="addLabel"
                            class="rounded-lg bg-blue-600 p-2 text-white"
                        >
                            <Plus :size="18" />
                        </button>
                    </div>

                    <div class="space-y-3">
                        <button
                            v-for="item in contact.labels"
                            :key="item.id ?? item.title"
                            @click="selectedLabelId = item.id"
                            class="w-full rounded-xl border p-3 text-left transition"
                            :class="
                                selectedLabelId === item.id
                                    ? 'border-blue-600 bg-blue-50'
                                    : 'border-gray-200 hover:border-blue-300'
                            "
                        >
                            <p class="font-medium">
                                {{ item.title || 'Untitled label' }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ item.type }}
                            </p>
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="col-span-8 rounded-xl border border-gray-200 bg-white p-6"
            >
                <div v-if="selectedLabel">
                    <div class="mb-5 flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">
                                Label Information
                            </h3>

                            <p class="text-sm text-gray-500">
                                Update selected label
                            </p>
                        </div>

                        <button
                            @click="removeLabel(selectedLabel.id)"
                            class="text-red-500"
                        >
                            <Trash2 :size="18" />
                        </button>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                Label Name
                            </label>

                            <input
                                v-model="selectedLabel.title"
                                class="w-full rounded-lg border px-3 py-2"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                Placeholder
                            </label>

                            <input
                                v-model="selectedLabel.placeholder"
                                class="w-full rounded-lg border px-3 py-2"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                Field Type
                            </label>

                            <select
                                v-model="selectedLabel.type"
                                class="w-full rounded-lg border px-3 py-2"
                            >
                                <option value="input">Input</option>

                                <option value="textarea">Textarea</option>

                                <option value="select">Select</option>
                            </select>
                        </div>
                        <div
                            v-if="selectedLabel.type === 'select'"
                            class="space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium">
                                    Select Options
                                </label>

                                <button
                                    type="button"
                                    @click="addOption"
                                    class="rounded-lg bg-blue-600 px-3 py-1 text-sm text-white"
                                >
                                    <Plus :size="16" />
                                </button>
                            </div>

                            <div
                                v-for="(option, index) in selectedLabel.options"
                                :key="index"
                                class="flex gap-2"
                            >
                                <input
                                    v-model="option.value"
                                    placeholder="Enter option"
                                    class="flex-1 rounded-lg border px-3 py-2"
                                />
                                <button
                                    type="button"
                                    @click="removeOption(index)"
                                    class="rounded-lg border border-red-300 px-3 text-red-600"
                                >
                                    <X :size="16" />
                                </button>
                            </div>

                            <p
                                v-if="!selectedLabel.options.length"
                                class="text-sm text-gray-400"
                            >
                                No options added.
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="flex h-80 items-center justify-center text-gray-400"
                >
                    Select a label to edit
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button
                @click="save"
                :disabled="loading"
                class="flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white disabled:opacity-50"
            >
                <Save :size="18" />
            </button>
        </div>
    </section>
</template>
