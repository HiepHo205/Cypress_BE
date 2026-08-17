<script setup>
import { reactive, ref, watch } from 'vue';
import { Save } from 'lucide-vue-next';
import { useToast } from 'vue-toastification';

import { useCaseStudy } from '@/composables/caseStudy/useCaseStudy';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({
            breadcrumb_first: '',
            breadcrumb_first_url: '',
            breadcrumb_second: '',
            breadcrumb_second_url: '',
            title: '',
            description: ''
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
    description: ''
});

const { loading: saving, updateItem } = useCaseStudy('banner');

watch(
    () => props.data,
    (data) => {
        form.breadcrumb_first = data?.breadcrumb_first ?? '';
        form.breadcrumb_first_url = data?.breadcrumb_first_url ?? '';

        form.breadcrumb_second = data?.breadcrumb_second ?? '';
        form.breadcrumb_second_url = data?.breadcrumb_second_url ?? '';

        form.title = data?.title ?? '';
        form.description = data?.description ?? '';
    },
    {
        immediate: true,
        deep: true
    }
);

const validateForm = () => {
    if (!form.breadcrumb_first.trim()) {
        toast.error('Please enter first breadcrumb.');
        return false;
    }

    if (!form.breadcrumb_first_url.trim()) {
        toast.error('Please enter first breadcrumb URL.');
        return false;
    }

    if (!form.breadcrumb_second.trim()) {
        toast.error('Please enter second breadcrumb.');
        return false;
    }

    if (!form.breadcrumb_second_url.trim()) {
        toast.error('Please enter second breadcrumb URL.');
        return false;
    }

    if (!form.title.trim()) {
        toast.error('Please enter title.');
        return false;
    }

    if (!form.description.trim()) {
        toast.error('Please enter description.');
        return false;
    }

    return true;
};

const save = async () => {
    if (saving.value) {
        return;
    }

    if (!validateForm()) {
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

        const result = await updateItem(input);

        emit('saved', result);
    } catch (error) {
        const message =
            error?.graphQLErrors?.[0]?.message ||
            error?.message ||
            'Failed to save case study banner.';

        toast.error(message);
    }
};
</script>

<template>
    <form @submit.prevent="save">
        <div class="rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-800">
                    Case Study Banner
                </h3>

                <p class="text-xs text-slate-500">
                    Manage the banner content displayed on the public Case Study
                    page.
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
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        />

                        <label
                            class="block text-xs font-semibold text-slate-700"
                        >
                            URL
                        </label>

                        <input
                            v-model="form.breadcrumb_first_url"
                            type="text"
                            placeholder="/"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
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
                            placeholder="Case Studies"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        />

                        <label
                            class="block text-xs font-semibold text-slate-700"
                        >
                            URL
                        </label>

                        <input
                            v-model="form.breadcrumb_second_url"
                            type="text"
                            placeholder="/case-studies"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        />
                    </div>
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700"
                    >
                        Title
                    </label>

                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="Enter a title"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    />
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700"
                    >
                        Description
                    </label>

                    <textarea
                        v-model="form.description"
                        rows="5"
                        placeholder="Enter banner description..."
                        class="w-full resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-sm leading-6 text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    ></textarea>
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
                    <Save
                        :size="17"
                        :class="{ 'animate-pulse': saving }"
                    />
                </button>
            </div>
        </div>
    </form>
</template>