<script setup>
import { reactive, ref, watch, onBeforeUnmount } from 'vue';
import { Save, Upload, X } from 'lucide-vue-next';
import { useToast } from 'vue-toastification';
import { useNews } from '@/composables/news/useNews';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({
            breadcrumb_first: '',
            breadcrumb_first_url: '',
            breadcrumb_second: '',
            breadcrumb_second_url: '',
            title: '',
            description: '',
            background_image: null
        })
    }
});

const emit = defineEmits(['saved']);

const toast = useToast();

const form = reactive({
    breadcrumb_first: '',
    breadcrumb_first_url: '',
    breadcrumb_second: '',
    breadcrumb_second_url: '',
    title: '',
    description: '',
    background_image: null
});

const previewImage = ref('');
const imageFile = ref(null);
const fileInput = ref(null);

const { loading: saving, updateItem } = useNews('banner');

watch(
    () => props.data,
    (data) => {
        form.breadcrumb_first = data?.breadcrumb_first ?? '';
        form.breadcrumb_first_url = data?.breadcrumb_first_url ?? '';
        form.breadcrumb_second = data?.breadcrumb_second ?? '';
        form.breadcrumb_second_url = data?.breadcrumb_second_url ?? '';
        form.title = data?.title ?? '';
        form.description = data?.description ?? '';
        form.background_image = data?.background_image ?? null;

        if (data?.background_image?.url) {
            previewImage.value = data.background_image.url;
        } else if (typeof data?.background_image === 'string') {
            previewImage.value = data.background_image;
        } else {
            previewImage.value = '';
        }

        imageFile.value = null;
    },
    {
        immediate: true,
        deep: true
    }
);

const openFilePicker = () => {
    fileInput.value?.click();
};

const handleImageChange = (event) => {
    const file = event.target.files?.[0];

    if (!file) {
        return;
    }

    if (!file.type.startsWith('image/')) {
        toast.error('Please select an image file.');
        event.target.value = '';
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        toast.error('Image size must not exceed 5MB.');
        event.target.value = '';
        return;
    }
    if (previewImage.value?.startsWith('blob:')) {
        URL.revokeObjectURL(previewImage.value);
    }

    imageFile.value = file;
    previewImage.value = URL.createObjectURL(file);
};

const removeImage = () => {
    if (previewImage.value?.startsWith('blob:')) {
        URL.revokeObjectURL(previewImage.value);
    }

    imageFile.value = null;
    previewImage.value = '';
    form.background_image = null;

    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const save = async () => {
    if (!form.breadcrumb_first.trim()) {
        toast.error('Please enter first breadcrumb.');
        return;
    }

    if (!form.breadcrumb_first_url.trim()) {
        toast.error('Please enter first breadcrumb URL.');
        return;
    }

    if (!form.breadcrumb_second.trim()) {
        toast.error('Please enter second breadcrumb.');
        return;
    }

    if (!form.breadcrumb_second_url.trim()) {
        toast.error('Please enter second breadcrumb URL.');
        return;
    }

    if (!form.title.trim()) {
        toast.error('Please enter title.');
        return;
    }

    if (!form.description.trim()) {
        toast.error('Please enter description.');
        return;
    }

    try {
        const input = {
            breadcrumb_first: form.breadcrumb_first.trim(),
            breadcrumb_first_url: form.breadcrumb_first_url.trim(),
            breadcrumb_second: form.breadcrumb_second.trim(),
            breadcrumb_second_url: form.breadcrumb_second_url.trim(),
            title: form.title.trim(),
            description: form.description.trim()
        };
        const result = await updateItem(input, imageFile.value);
        emit('saved');
    } catch (error) {
        console.error('News banner update error:', error);

        const message =
            error?.graphQLErrors?.[0]?.message ||
            error?.message ||
            'Failed to save news banner.';

        toast.error(message);
    }
};

onBeforeUnmount(() => {
    if (previewImage.value?.startsWith('blob:')) {
        URL.revokeObjectURL(previewImage.value);
    }
});
</script>

<template>
    <form class="mt-7" @submit.prevent="save">
        <div class="rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-800">
                    News Banner
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Manage the banner content displayed on the public News page.
                </p>
            </div>

            <div class="space-y-6 p-5">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-700"
                        >
                            Breadcrumb First
                        </label>

                        <input
                            v-model="form.breadcrumb_first"
                            type="text"
                            placeholder="Home"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        />

                        <label
                            class="mt-4 block text-xs font-semibold text-slate-700"
                        >
                            URL
                        </label>

                        <input
                            v-model="form.breadcrumb_first_url"
                            type="text"
                            placeholder="/"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-700"
                        >
                            Breadcrumb Second
                        </label>

                        <input
                            v-model="form.breadcrumb_second"
                            type="text"
                            placeholder="News"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        />

                        <label
                            class="mt-4 block text-xs font-semibold text-slate-700"
                        >
                            URL
                        </label>

                        <input
                            v-model="form.breadcrumb_second_url"
                            type="text"
                            placeholder="/news"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">
                        Title
                    </label>

                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="Enter a title"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">
                        Description
                    </label>

                    <textarea
                        v-model="form.description"
                        rows="5"
                        placeholder="Expert advice for every stage of your startup journey..."
                        class="mt-2 w-full resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-sm leading-6 text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700">
                        Background Image
                    </label>

                    <input
                        ref="fileInput"
                        type="file"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="hidden"
                        @change="handleImageChange"
                    />

                    <div
                        v-if="previewImage"
                        class="relative mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100"
                    >
                        <img
                            :src="previewImage"
                            alt="News banner background"
                            class="h-64 w-full object-cover"
                        />

                        <button
                            type="button"
                            class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-600 shadow-md transition hover:bg-red-50 hover:text-red-500"
                            @click="removeImage"
                        >
                            <X :size="17" />
                        </button>

                        <div class="absolute bottom-3 left-3">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-md transition hover:bg-slate-50"
                                @click="openFilePicker"
                            >
                                <Upload :size="15" />
                                Change Image
                            </button>
                        </div>

                        <div
                            v-if="imageFile"
                            class="absolute bottom-3 right-3 rounded-xl bg-black/70 px-3 py-2 text-xs font-medium text-white"
                        >
                            {{ imageFile.name }}
                        </div>
                    </div>
                    <button
                        v-else
                        type="button"
                        class="mt-3 flex h-52 w-full flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 transition hover:border-blue-400 hover:bg-blue-50/50"
                        @click="openFilePicker"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-slate-400 shadow-sm"
                        >
                            <Upload :size="21" />
                        </div>

                        <span class="mt-3 text-sm font-semibold text-slate-700">
                            Upload Background Image
                        </span>

                        <span class="mt-1 text-xs text-slate-400">
                            PNG, JPG, JPEG or WEBP up to 5MB
                        </span>
                    </button>
                </div>
            </div>

            <div
                class="flex items-center justify-end border-t border-slate-100 bg-slate-50/50 px-5 py-4"
            >
                <button
                    type="submit"
                    :disabled="saving"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <Save :size="17" :class="{ 'animate-pulse': saving }" />

                    {{ saving ? 'Saving...' : 'Save' }}
                </button>
            </div>
        </div>
    </form>
</template>
