<script setup lang="ts">
import { ref, watch } from 'vue';
import { useToast } from 'vue-toastification';

import { useHomepage } from '../../../composables/home/useHomepage';

const toast = useToast();

const image = ref<File | null>(null);
const imagePreview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const title = ref('');
const description = ref('');

const primaryButtonText = ref('');
const primaryButtonUrl = ref('');

const secondaryButtonText = ref('');
const secondaryButtonUrl = ref('');

const { saveSection, getSection } = useHomepage();

const banner = getSection('banner');

watch(
    banner,
    (data) => {
        if (!data) return;

        title.value = data.title ?? '';

        description.value = data.description ?? '';

        primaryButtonText.value =
            data.primary_button_text ?? data.primaryButtonText ?? '';

        primaryButtonUrl.value =
            data.primary_button_url ?? data.primaryButtonUrl ?? '';

        secondaryButtonText.value =
            data.secondary_button_text ?? data.secondaryButtonText ?? '';

        secondaryButtonUrl.value =
            data.secondary_button_url ?? data.secondaryButtonUrl ?? '';

        if (data.image?.url) {
            imagePreview.value = data.image.url;
        }
    },
    {
        immediate: true,
        deep: true
    }
);

const openFilePicker = () => {
    fileInput.value?.click();
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;

    const file = target.files?.[0];

    if (!file) return;

    image.value = file;

    imagePreview.value = URL.createObjectURL(file);
};

const handleSave = async () => {
    try {

        await saveSection(
            'banner',
            {
                title: title.value,
                description: description.value,
                primary_button_text: primaryButtonText.value,
                primary_button_url: primaryButtonUrl.value,
                secondary_button_text: secondaryButtonText.value,
                secondary_button_url: secondaryButtonUrl.value
            },
            image.value
        );

    } catch (error) {
        console.error(error);
    }
};

const handleCancel = () => {
    image.value = null;

    if (fileInput.value) {
        fileInput.value.value = '';
    }
};
</script>

<template>
    <div class="rounded-2xl bg-white p-8 shadow-sm">
        <div class="mb-8">
            <h2 class="text-3xl font-semibold text-gray-900">
                Homepage Banner
            </h2>

            <p class="mt-2 text-gray-500">
                Manage banner images and homepage content.
            </p>
        </div>

        <div class="mb-8">
            <label
                class="mb-3 block text-sm font-semibold uppercase text-gray-600"
            >
                Banner Image
            </label>

            <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleFileChange"
            />

            <div
                class="cursor-pointer rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-6"
                @click="openFilePicker"
            >
                <div class="flex h-64 items-center justify-center">
                    <img
                        v-if="imagePreview"
                        :src="imagePreview"
                        class="max-h-full max-w-full rounded-lg object-contain"
                    />

                    <div v-else class="text-center text-gray-400">
                        Click to upload banner image
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label
                class="mb-2 block text-sm font-semibold uppercase text-gray-600"
            >
                Title
            </label>

            <input
                v-model="title"
                type="text"
                placeholder="Enter banner title"
                class="w-full rounded-lg border border-gray-300 px-4 py-3"
            />
        </div>

        <div class="mb-8">
            <label
                class="mb-2 block text-sm font-semibold uppercase text-gray-600"
            >
                Description
            </label>

            <textarea
                v-model="description"
                rows="5"
                placeholder="Enter banner description"
                class="w-full rounded-lg border border-gray-300 px-4 py-3"
            />
        </div>

        <div class="mb-8">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Buttons</h3>

                <p class="mt-1 text-sm text-gray-500">
                    Configure the primary and secondary buttons displayed on the
                    banner.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Primary Button -->
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-900">
                                Primary Button
                            </h4>

                            <p class="mt-1 text-xs text-gray-500">
                                Main call-to-action button.
                            </p>
                        </div>

                        <span
                            class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700"
                        >
                            Primary
                        </span>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Button Text
                            </label>

                            <input
                                v-model="primaryButtonText"
                                type="text"
                                placeholder="Get Started"
                                class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Button URL
                            </label>

                            <input
                                v-model="primaryButtonUrl"
                                type="text"
                                placeholder="/pricing"
                                class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                            />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-900">
                                Secondary Button
                            </h4>

                            <p class="mt-1 text-xs text-gray-500">
                                Secondary call-to-action button.
                            </p>
                        </div>

                        <span
                            class="rounded-full bg-gray-200 px-3 py-1 text-xs font-medium text-gray-700"
                        >
                            Secondary
                        </span>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Button Text
                            </label>

                            <input
                                v-model="secondaryButtonText"
                                type="text"
                                placeholder="Learn More"
                                class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Button URL
                            </label>

                            <input
                                v-model="secondaryButtonUrl"
                                type="text"
                                placeholder="/about"
                                class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button
                type="button"
                @click="handleCancel"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-gray-700"
            >
                Cancel
            </button>

            <button
                type="button"
                @click="handleSave"
                class="rounded-lg bg-blue-600 px-6 py-2.5 font-medium text-white"
            >
                Save
            </button>
        </div>
    </div>
</template>
