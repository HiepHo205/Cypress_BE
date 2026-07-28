<script setup>
import { onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import { Edit, Plus, Save, X } from 'lucide-vue-next';
import useFooterBottomBarManager from '@/composables/footer/useFooterBottomBarManager';

const emit = defineEmits(['loading']);
const toast = useToast();
const {
    form,
    saving,
    editing,
    hasData,
    loadBottomBar,
    editBottomBar,
    cancelEdit,
    addLegalLink,
    removeNewLegalLink,
    removeExistingLegalLink,
    saveBottomBar
} = useFooterBottomBarManager();

onMounted(async () => {
    emit('loading', true);

    try {
        await loadBottomBar();
    } catch (error) {
        console.log(error);
        toast.error('Failed to load footer bottom bar');
    } finally {
        emit('loading', false);
    }
});
</script>

<template>
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Footer Bottom Bar
                </h2>

                <p class="mt-2 text-sm text-gray-500">
                    Configure copyright information and legal navigation links displayed at the bottom of the footer.
                </p>
            </div>

            <button v-if="!editing" @click="editBottomBar"
                class="flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-white hover:bg-blue-700">
                <Edit :size="16" />
                Update
            </button>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">

            <div v-if="!hasData && !editing" class="flex items-center justify-center py-16">
                <p class="text-gray-400 text-lg">
                    No footer bottom bar data
                </p>
            </div>

            <div v-else class="space-y-6">

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Copyright Text
                    </label>

                    <textarea v-model="form.copyright" :disabled="!editing" rows="3"
                        placeholder="© 2024 Cypress Hub. All rights reserved."
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none disabled:bg-gray-100" />
                </div>

                <div>
                    <label class="mb-3 block text-sm font-medium text-gray-700">
                        Legal Links
                    </label>

                    <div v-if="form.legal_links.length">
                        <div v-for="(link, index) in form.legal_links" :key="index"
                            class="mb-4 grid grid-cols-12 gap-4">

                            <div class="col-span-6">
                                <input v-model="link.text" :disabled="!editing" type="text" placeholder="Privacy Policy"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none disabled:bg-gray-100" />
                            </div>

                            <div class="col-span-5">
                                <input v-model="link.url" :disabled="!editing" type="text" placeholder="/privacy-policy"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none disabled:bg-gray-100" />
                            </div>

                            <div class="col-span-1">
                                <button v-if="editing"
                                    @click="link.isNew ? removeNewLegalLink(index) : removeExistingLegalLink(index)"
                                    class="flex w-full items-center justify-center rounded-lg bg-red-500 px-4 py-3 text-white hover:bg-red-600">
                                    <X :size="16" />
                                </button>
                            </div>

                        </div>
                    </div>

                    <div v-else class="py-4 text-sm text-gray-400">
                        No legal links available
                    </div>

                    <button v-if="editing" @click="addLegalLink"
                        class="mt-3 flex items-center gap-2 rounded-lg border border-blue-600 px-5 py-2 text-blue-600 hover:bg-blue-50">
                        <Plus :size="16" />
                        Add Legal Link
                    </button>
                </div>

                <div v-if="editing" class="flex justify-end gap-3 pt-4 border-t border-gray-100">

                    <button @click="cancelEdit"
                        class="flex items-center gap-2 rounded-lg bg-red-500 px-6 py-3 text-white hover:bg-red-600">
                        <X :size="16" />
                        Cancel
                    </button>

                    <button @click="saveBottomBar" :disabled="saving"
                        class="flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-white disabled:opacity-50">
                        <Save :size="16" />
                        {{ saving ? 'Saving...' : 'Save' }}
                    </button>

                </div>

            </div>

        </div>
    </div>
</template>