<script setup>
import { ref, onMounted } from "vue";
import useHeaderLogo from "../../../../composables/header/useHeaderLogo";
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';

const props = defineProps({
    hideHeaderInfo: {
        type: Boolean,
        default: false
    }
});

const fileInput = ref(null);
const preview = ref("");
const selectedFile = ref(null);
const fileName = ref("Current Logo");
const pageLoading = ref(true);

const {
    logo: currentLogo,
    loading,
    getLogo,
    updateLogo
} = useHeaderLogo();

onMounted(async () => {
    try {
        await getLogo();
    } finally {
        pageLoading.value = false;
    }
});

const handleFileChange = (event) => {
    const file = event.target.files[0];

    if (!file)
        return;

    if (!file.type.startsWith("image/"))
        return;

    if (file.size > 2 * 1024 * 1024)
        return;

    selectedFile.value = file;
    fileName.value = file.name;
    preview.value = URL.createObjectURL(file);
};

const saveLogo = async () => {
    if (!selectedFile.value)
        return;

    const result = await updateLogo(selectedFile.value);

    if (!result)
        return;

    preview.value = "";
    selectedFile.value = null;
    fileName.value = "Current Logo";

    if (fileInput.value) {
        fileInput.value.value = "";
    }
};
</script>

<template>
    <div class="relative rounded-2xl">
        <LoadingOverlay :show="pageLoading" message="Loading logo..." :fullScreen="false" />

        <div :class="pageLoading ? 'pointer-events-none opacity-50' : ''">

            <template v-if="!props.hideHeaderInfo">
                <h2 class="text-lg font-semibold text-gray-900">
                    Website Logo
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Configure the primary logo displayed in the website header.
                </p>
            </template>

            <div class="mt-6 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-10">
                <div class="flex flex-col items-center">

                    <img :src="preview || currentLogo || 'https://placehold.co/240x80?text=Cypress+Logo'"
                        alt="Website Logo" class="h-32 w-72 object-contain" />

                    <p class="mt-6 text-base font-semibold text-gray-700">
                        {{ fileName }}
                    </p>

                    <p class="mt-2 text-sm text-gray-500">
                        PNG, JPG, JPEG • Max 2MB
                    </p>

                    <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="handleFileChange" />

                    <button
                        class="mt-6 rounded-xl border border-gray-300 px-5 py-2 text-sm font-medium transition hover:bg-gray-100"
                        @click="fileInput.click()">
                        Choose Image
                    </button>

                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button :disabled="!selectedFile || loading"
                    class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="saveLogo">
                    {{ loading ? "Saving..." : "Save Changes" }}
                </button>
            </div>

        </div>
    </div>
</template>