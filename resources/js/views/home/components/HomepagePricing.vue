<script setup lang="ts">
import { reactive, watch } from 'vue';
import { Save } from 'lucide-vue-next';

import { useHomepage } from '../../../composables/home/useHomepage';

const { loading, getSection, saveSection } = useHomepage();

const pricing = getSection('pricing');

const form = reactive({
    title: '',
    description: '',
    button_text: '',
    button_link: ''
});

watch(
    pricing,
    (value) => {
        if (!value) return;

        form.title = value.title ?? '';

        form.description = value.description ?? '';

        form.button_text = value.button_text ?? '';

        form.button_link = value.button_link ?? '';
    },
    {
        immediate: true,
        deep: true
    }
);

const save = async () => {
    await saveSection('pricing', {
        title: form.title,

        description: form.description,

        button_text: form.button_text,

        button_link: form.button_link
    });
};
</script>

<template>
    <section class="space-y-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Homepage Pricing
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Manage pricing banner on homepage.
            </p>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="space-y-5">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Title
                    </label>

                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="Enter pricing title"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-600"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Description
                    </label>

                    <textarea
                        v-model="form.description"
                        rows="5"
                        placeholder="Enter pricing description"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-600"
                    />
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Button Text
                        </label>

                        <input
                            v-model="form.button_text"
                            type="text"
                            placeholder="Enter button text"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-600"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700"
                        >
                            Button Link
                        </label>

                        <input
                            v-model="form.button_link"
                            type="text"
                            placeholder="/pricing"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-blue-600"
                        />
                    </div>
                </div>

                <div class="flex justify-end border-t border-gray-100 pt-5">
                    <button
                        @click="save"
                        :disabled="loading"
                        class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        <Save :size="18" />

                        {{ loading ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>
