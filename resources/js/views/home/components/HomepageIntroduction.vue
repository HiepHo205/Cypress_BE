<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { Save } from 'lucide-vue-next';
import { useHomepage } from '../../../composables/home/useHomepage';

const { loading, getSection, saveSection } = useHomepage();

const section = getSection('introduction');

const fileInput = ref<HTMLInputElement | null>(null);
const previewImage = ref('');
const selectedImage = ref<File | null>(null);

const form = ref({
    title: '',
    description: '',
    author: '',
    position: ''
});

const handleImageClick = () => {
    fileInput.value?.click();
};

const uploadImage = (event: Event) => {
    const target = event.target as HTMLInputElement;

    if (!target.files || !target.files.length) {
        return;
    }

    const file = target.files[0];

    selectedImage.value = file;

    previewImage.value = URL.createObjectURL(file);
};

const loadIntroduction = () => {
    const data = section.value;

    if (!data) {
        return;
    }

    form.value = {
        title: data.title ?? '',
        description: data.description ?? '',
        author: data.author ?? '',
        position: data.position ?? ''
    };

    previewImage.value = data.image?.url ?? '';
};

watch(
    section,
    () => {
        loadIntroduction();
    },
    {
        immediate: true,
        deep: true
    }
);

const save = async () => {
    await saveSection(
        'introduction',
        {
            title: form.value.title,
            description: form.value.description,
            author: form.value.author,
            position: form.value.position
        },
        selectedImage.value
    );

    selectedImage.value = null;

    loadIntroduction();
};

onMounted(() => {
    loadIntroduction();
});
</script>

<template>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Company Introduction
            </h2>

            <p class="mt-1 text-gray-500">
                Manage the company introduction section.
            </p>
        </div>

        <div class="grid gap-5 xl:grid-cols-12">
            <div class="xl:col-span-4">
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Background Image
                </label>

                <input
                    ref="fileInput"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="uploadImage"
                />

                <div
                    @click="handleImageClick"
                    class="flex aspect-[4/5] max-w-[280px] cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-100 bg-gray-50 p-3 hover:border-blue-500"
                >
                    <img
                        v-if="previewImage"
                        :src="previewImage"
                        class="max-h-full max-w-full rounded-lg object-contain"
                    />

                    <span v-else class="text-sm text-gray-400">
                        Click image to upload
                    </span>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100  bg-white p-5 shadow-sm xl:col-span-8">
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium">
                            Quote Title
                        </label>

                        <input
                            v-model="form.title"
                            class="w-full rounded-lg border border-gray-100 px-3 py-2"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">
                            Introduction
                        </label>

                        <textarea
                            v-model="form.description"
                            rows="5"
                            class="w-full rounded-lg border border-gray-100 px-3 py-2"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium">
                                Author
                            </label>

                            <input
                                v-model="form.author"
                                class="w-full rounded-lg border border-gray-100 px-3 py-2"
                            />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">
                                Position
                            </label>

                            <input
                                v-model="form.position"
                                class="w-full rounded-lg border border-gray-100 px-3 py-2"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">

                        <button
                            @click="save"
                            :disabled="saving"
                            class="flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-white"
                        >
                            <Save :size="18" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
