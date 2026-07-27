<script setup>
import { reactive, ref } from 'vue';

const iconFile = ref(null);
const iconPreview = ref(null);

const form = reactive({
    title: '',
    description: '',
    placeholder: '',
    button_icon: '',
});

const handleIconChange = (event) => {
    const file = event.target.files[0];

    if (!file) return;

    const allowedTypes = [
        'image/png',
        'image/jpeg',
        'image/svg+xml'
    ];

    if (!allowedTypes.includes(file.type)) {
        alert('Only PNG, JPG or SVG files are allowed');
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        alert('Maximum file size is 2MB');
        return;
    }

    iconFile.value = file;
    iconPreview.value = URL.createObjectURL(file);

    form.button_icon = file;
};
</script>

<template>
    <div class="space-y-8">

        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Newsletter
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Configure the newsletter subscription section displayed in the website footer.
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">

            <div class="space-y-6">

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Section Title
                    </label>

                    <input v-model="form.title" type="text" placeholder="Ecosystem News"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Description
                    </label>

                    <textarea v-model="form.description" rows="4"
                        placeholder="Get the monthly digest of exit reports and growth tactics."
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Email Placeholder
                    </label>

                    <input v-model="form.placeholder" type="text" placeholder="example@company.com"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Button Icon
                    </label>

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-lg border border-dashed border-gray-300 bg-gray-50">
                            <img v-if="iconPreview" :src="iconPreview" class="h-full w-full object-contain" />

                            <span v-else class="text-xs text-gray-400">
                                Icon
                            </span>
                        </div>

                        <label
                            class="cursor-pointer rounded-lg bg-blue-600 px-5 py-2 text-white transition hover:bg-blue-700">
                            Upload Icon

                            <input type="file" class="hidden" accept="image/png,image/jpeg,image/svg+xml"
                                @change="handleIconChange" />
                        </label>

                    </div>

                    <p class="mt-2 text-xs text-gray-500">
                        PNG, JPG or SVG • Max 2 MB
                    </p>
                </div>

            </div>

        </div>

    </div>
</template>