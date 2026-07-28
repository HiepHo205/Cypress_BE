<script setup>
import { ref, reactive, onMounted } from 'vue';
import LogoSection from '@/views/cms/header/components/LogoSection.vue';
import useFooterBranding from '../../../../composables/footer/useFooterBranding';

const emit = defineEmits(['loading']);

const savingInfo = ref(false);

const form = reactive({
    company_name: '',
    description: ''
});

const { getBranding, updateBranding } = useFooterBranding();


onMounted(async () => {

    emit('loading', true);

    try {

        const data = await getBranding();

        if (data) {
            form.company_name = data.company_name ?? '';
            form.description = data.description ?? '';
        }

    } catch (error) {

        console.error('Load footer branding error:', error);

    } finally {

        emit('loading', false);

    }

});


const saveBranding = async () => {

    emit('loading', true);

    try {

        await updateBranding({
            company_name: form.company_name,
            description: form.description
        });

    } catch (error) {

        console.error('Save branding error:', error);

    } finally {

        emit('loading', false);

    }

};
</script>
<template>
    <div class="relative rounded-2xl">

        <div :class="pageLoading ? 'pointer-events-none opacity-50' : ''">
            <h2 class="text-lg font-semibold">Footer Branding</h2>

            <p class="mt-1 text-sm text-gray-500">
                Configure footer logo and company information.
            </p>

            <div class="mt-6 rounded-2xl bg-white p-6">
                <h3 class="mb-5 text-lg font-semibold">Footer Logo</h3>

                <LogoSection :hideHeaderInfo="true" />
            </div>

            <div class="border-t border-gray-300"></div>

            <div class="mt-6 rounded-2xl bg-white p-6">
                <h3 class="mb-5 text-lg font-semibold">Company Information</h3>

                <input v-model="form.company_name" placeholder="Company Name"
                    class="mb-4 w-full rounded-lg border px-4 py-3" />

                <textarea v-model="form.description" rows="4" placeholder="Description"
                    class="w-full rounded-lg border px-4 py-3" />

                <div class="mt-5 flex justify-end">
                    <button @click="saveBranding" :disabled="savingInfo"
                        class="rounded-xl bg-blue-600 px-6 py-2.5 text-white disabled:opacity-50">
                        {{ savingInfo ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>