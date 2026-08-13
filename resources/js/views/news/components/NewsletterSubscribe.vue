<script setup>
import { reactive, watch } from 'vue';
import { Save } from 'lucide-vue-next';
import { useToast } from 'vue-toastification';
import { useNews } from '@/composables/news/useNews';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({
            title1: '',
            title2: '',
            title: '',
            description: '',
            inputPlaceholder: '',
            buttonText: '',
            buttonUrl: ''
        })
    }
});

const emit = defineEmits(['saved']);

const toast = useToast();

const form = reactive({
    title1: '',
    title2: '',
    title: '',
    description: '',
    inputPlaceholder: '',
    buttonText: '',
    buttonUrl: ''
});

const { loading: saving, updateItem } = useNews('newsletter');

watch(
    () => props.data,
    (data) => {
        form.title1 = data?.title1 ?? '';
        form.title2 = data?.title2 ?? '';

        form.title = data?.title ?? '';
        form.description = data?.description ?? '';

        form.inputPlaceholder =
            data?.inputPlaceholder ?? data?.placeholder ?? '';

        form.buttonText = data?.buttonText ?? data?.buttonLabel ?? '';

        form.buttonUrl = data?.buttonUrl ?? data?.url ?? '';
    },
    {
        immediate: true,
        deep: true
    }
);

const save = async () => {
    if (!form.title1.trim()) {
        toast.error('Please enter title 1.');
        return;
    }

    if (!form.title2.trim()) {
        toast.error('Please enter title 2.');
        return;
    }

    if (!form.title.trim()) {
        toast.error('Please enter a heading.');
        return;
    }

    if (!form.description.trim()) {
        toast.error('Please enter a description.');
        return;
    }

    if (!form.inputPlaceholder.trim()) {
        toast.error('Please enter an input placeholder.');
        return;
    }

    if (!form.buttonText.trim()) {
        toast.error('Please enter button text.');
        return;
    }

    if (!form.buttonUrl.trim()) {
        toast.error('Please enter button URL.');
        return;
    }

    try {
        await updateItem({
            title1: form.title1.trim(),
            title2: form.title2.trim(),

            title: form.title.trim(),
            description: form.description.trim(),
            inputPlaceholder: form.inputPlaceholder.trim(),
            buttonText: form.buttonText.trim(),
            buttonUrl: form.buttonUrl.trim()
        });

        toast.success('Newsletter updated successfully.');

        emit('saved');
    } catch (error) {
        console.error('Failed to save newsletter:', error);

        toast.error(error?.message || 'Failed to save newsletter.');
    }
};
</script>

<template>
    <form class="mt-7" @submit.prevent="save">
        <!-- =========================================================
             NEWSLETTER TITLES
        ========================================================== -->
        <div class="mb-5 rounded-2xl border border-slate-200 bg-white">
            <!-- Header -->
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-800">
                    Newsletter Titles
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Manage the two titles displayed in the newsletter section.
                </p>
            </div>

            <!-- Content -->
            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
                <!-- Title 1 -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700">
                        Title 1
                    </label>

                    <input
                        v-model="form.title1"
                        type="text"
                        placeholder="Enter first title"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    />
                </div>

                <!-- Title 2 -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700">
                        Title 2
                    </label>

                    <input
                        v-model="form.title2"
                        type="text"
                        placeholder="Enter second title"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    />
                </div>
            </div>
        </div>

        <!-- =========================================================
             NEWSLETTER CONTENT
        ========================================================== -->
        <div class="rounded-2xl border border-slate-200 bg-white">
            <!-- Header -->
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-800">
                    Newsletter Content
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Manage the newsletter content displayed on the News page.
                </p>
            </div>

            <!-- Content -->
            <div class="space-y-5 p-5">
                <!-- Heading -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700">
                        Heading
                    </label>

                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="Enter a title"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    />
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700">
                        Description
                    </label>

                    <textarea
                        v-model="form.description"
                        rows="5"
                        placeholder="Join hundreds of founders who've turned their vision into unicorns..."
                        class="mt-2 w-full resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-sm leading-6 text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    ></textarea>
                </div>

                <!-- Input Placeholder -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700">
                        Input Placeholder
                    </label>

                    <input
                        v-model="form.inputPlaceholder"
                        type="text"
                        placeholder="Enter your email"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    />
                </div>

                <!-- Button -->
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <!-- Button Text -->
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-700"
                        >
                            Button Text
                        </label>

                        <input
                            v-model="form.buttonText"
                            type="text"
                            placeholder="Get Updates"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        />
                    </div>

                    <!-- Button URL -->
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-700"
                        >
                            Button URL
                        </label>

                        <input
                            v-model="form.buttonUrl"
                            type="text"
                            placeholder="/newsletter"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        />
                    </div>
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
                    <Save :size="17" />

                    <span>
                        {{ saving ? 'Saving...' : 'Save' }}
                    </span>
                </button>
            </div>
        </div>
    </form>
</template>
