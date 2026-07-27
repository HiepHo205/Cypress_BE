<script setup>
import { reactive } from 'vue';

const socials = reactive([
    {
        name: '',
        url: '',
    },
]);

const addSocial = () => {
    socials.push({
        name: '',
        url: '',
    });
};

const removeSocial = (index) => {
    socials.splice(index, 1);
};
</script>

<template>
    <div class="space-y-8">

        <!-- Header -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Social Links
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Configure the social media links displayed in the website footer.
            </p>
        </div>

        <div class="rounded-2xl border bg-white p-6">

            <div class="space-y-4">

                <div v-for="(social, index) in socials" :key="index" class="grid grid-cols-12 gap-4">

                    <!-- Social Name -->
                    <div class="col-span-4">

                        <label class="mb-2 block text-sm font-medium">
                            Social Name
                        </label>

                        <input v-model="social.name" type="text" placeholder="Facebook"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none" />

                    </div>

                    <!-- URL -->
                    <div class="col-span-6">

                        <label class="mb-2 block text-sm font-medium">
                            URL
                        </label>

                        <input v-model="social.url" type="url" placeholder="https://facebook.com/yourpage"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none" />

                    </div>

                    <!-- Remove -->
                    <div class="col-span-2 flex items-end">

                        <button v-if="socials.length > 1" @click="removeSocial(index)"
                            class="w-full rounded-lg bg-red-500 px-4 py-3 text-white transition hover:bg-red-600">
                            Remove
                        </button>

                    </div>

                </div>

                <button @click="addSocial"
                    class="rounded-lg border border-blue-600 px-5 py-2 font-medium text-blue-600 transition hover:bg-blue-50">
                    + Add Social Link
                </button>

            </div>

        </div>

    </div>
</template>