<script setup>
import { reactive } from 'vue';

const form = reactive({
    copyright: '',
    legal_links: [
        {
            text: 'Privacy Policy',
            url: '/privacy-policy'
        }
    ]
});


const addLegalLink = () => {
    form.legal_links.push({
        text: '',
        url: ''
    });
};


const removeLegalLink = (index) => {
    form.legal_links.splice(index, 1);
};
</script>


<template>

    <div class="space-y-8">

        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Footer Bottom Bar
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Configure copyright information and legal navigation links displayed at the bottom of the footer.
            </p>
        </div>


        <div class="rounded-xl border border-gray-200 bg-white p-6">

            <div class="space-y-6">


                <div>

                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Copyright Text
                    </label>


                    <textarea v-model="form.copyright" rows="3" placeholder="© 2024 Cypress Hub. All rights reserved."
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none" />

                </div>



                <div>

                    <label class="mb-3 block text-sm font-medium text-gray-700">
                        Legal Links
                    </label>



                    <div v-for="(link, index) in form.legal_links" :key="index" class="mb-4 grid grid-cols-12 gap-4">


                        <div class="col-span-5">

                            <input v-model="link.text" type="text" placeholder="Privacy Policy"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none" />

                        </div>



                        <div class="col-span-5">

                            <input v-model="link.url" type="text" placeholder="/privacy-policy"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none" />

                        </div>



                        <div class="col-span-2">

                            <button v-if="form.legal_links.length > 1" @click="removeLegalLink(index)"
                                class="w-full rounded-lg bg-red-500 px-4 py-3 text-white hover:bg-red-600">
                                Remove
                            </button>

                        </div>


                    </div>



                    <button @click="addLegalLink"
                        class="rounded-lg border border-blue-600 px-5 py-2 text-blue-600 hover:bg-blue-50">
                        + Add Legal Link
                    </button>


                </div>


            </div>


        </div>


    </div>

</template>