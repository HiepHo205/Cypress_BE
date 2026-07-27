<script setup>
import { reactive } from 'vue';

const navigations = reactive([
    {
        group: 'Company',
        title: '',
        link: '',
    },
]);

const groups = [
    'Company',
    'Contact Us',
];

const addNavigation = () => {
    navigations.push({
        group: 'Company',
        title: '',
        link: '',
    });
};

const removeNavigation = (index) => {
    navigations.splice(index, 1);
};
</script>

<template>
    <div class="space-y-8">

        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Footer Navigation
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Configure footer navigation menus.
            </p>
        </div>

        <div class="rounded-2xl border bg-white p-6">

            <div class="space-y-5">

                <div v-for="(item, index) in navigations" :key="index" class="grid grid-cols-12 gap-4">

                    <!-- Group -->
                    <div class="col-span-3">

                        <label class="mb-2 block text-sm font-medium">
                            Group
                        </label>

                        <select v-model="item.group"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none">
                            <option v-for="group in groups" :key="group" :value="group">
                                {{ group }}
                            </option>
                        </select>

                    </div>

                    <!-- Navigation -->
                    <div class="col-span-4">

                        <label class="mb-2 block text-sm font-medium">
                            Navigation
                        </label>

                        <input v-model="item.title" placeholder="About us"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none" />

                    </div>

                    <!-- Link -->
                    <div class="col-span-4">

                        <label class="mb-2 block text-sm font-medium">
                            Link
                        </label>

                        <input v-model="item.link" placeholder="/about"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none" />

                    </div>

                    <!-- Remove -->
                    <div class="col-span-1 flex items-end">

                        <button v-if="navigations.length > 1" @click="removeNavigation(index)"
                            class="w-full rounded-lg bg-red-500 py-3 text-white hover:bg-red-600">
                            ✕
                        </button>

                    </div>

                </div>

                <button @click="addNavigation"
                    class="rounded-lg border border-blue-600 px-5 py-2 text-blue-600 hover:bg-blue-50">
                    + Add Navigation
                </button>

            </div>

        </div>

    </div>
</template>