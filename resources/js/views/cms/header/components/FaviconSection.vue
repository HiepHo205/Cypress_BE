<script setup>
import { ref, onMounted } from 'vue';
import useHeaderFavicon from '@/composables/header/useHeaderFavicon';

const {
    getFavicon,
    updateFavicon
} = useHeaderFavicon();
const emit = defineEmits(["loading"]);
const file = ref(null);
const preview = ref('');
const fileName = ref('');
const fileFormat = ref('');
onMounted(async () => {
    emit("loading", true);
    try {
        const data = await getFavicon();
        if (data?.url) {
            preview.value = data.url;
            fileName.value = data.url
                .split("/")
                .pop();
            fileFormat.value = data.url
                .split(".")
                .pop()
                .toUpperCase();
        }
    } finally {
        emit("loading", false);
    }
});
const handleUpload = (event) => {
    const selectedFile = event.target.files[0];

    if (!selectedFile) {
        return;
    }

    file.value = selectedFile;

    fileName.value = selectedFile.name;

    fileFormat.value = selectedFile.name
        .split('.')
        .pop()
        .toUpperCase();

    preview.value = URL.createObjectURL(selectedFile);
};
const save = async () => {
    if (!file.value) {
        return;
    }
    try {
        const result = await updateFavicon(file.value);
        if (result?.url) {
            preview.value = result.url;
            fileName.value = result.url
                .split("/")
                .pop();
            fileFormat.value = result.url
                .split(".")
                .pop()
                .toUpperCase();
        }
    } finally {
        emit("loading", false);
    }
};
</script>

<template>
    <div class="relative rounded-2xl">
        <h2 class="text-lg font-semibold text-gray-900">
            Favicon
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Configure the favicon displayed in the browser tab.
        </p>
        <div class="mt-6 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-10">
            <div class="flex flex-col items-center">
                <div
                    class="flex h-20 w-20 items-center justify-center rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <img v-if="preview" :src="preview" alt="Favicon" class="h-12 w-12 object-contain" />
                    <span v-else class="text-xl font-bold text-gray-400">
                        F
                    </span>
                </div>
                <p class="mt-6 text-base font-semibold text-gray-700">
                    {{ fileName || 'No favicon uploaded' }}
                </p>
                <p class="mt-2 text-sm text-gray-500">
                    {{ fileFormat || 'ICO / PNG / SVG' }}
                </p>
            </div>
            <div class="mt-6 flex justify-center">
                <label
                    class="cursor-pointer rounded-xl bg-blue-600 px-6 py-3 text-sm font-medium text-white hover:bg-blue-700">
                    Upload Favicon
                    <input type="file" accept=".ico,.png,.svg" class="hidden" @change="handleUpload" />
                </label>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    File Name
                </label>
                <input :value="fileName" readonly
                    class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-700 outline-none" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Format
                </label>
                <input :value="fileFormat" readonly
                    class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-700 outline-none" />
            </div>
        </div>
        <div class="mt-8 flex justify-end">
            <button @click="save"
                class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700">
                Save Changes
            </button>
        </div>

    </div>
</template>