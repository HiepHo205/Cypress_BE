<script setup>
import { reactive, onMounted } from 'vue';
import ConfirmModal from '../../../../components/common/ConfirmModal.vue';
import useSocialManager from '@/composables/footer/useSocialManager';
import { Edit, Plus, Save, Trash2, X } from 'lucide-vue-next';

const socials = reactive([]);
const emit = defineEmits(['loading']);

const { saving, showDeleteModal, loadSocial, saveSocial, confirmDelete, deleteSocial, cancelDelete } = useSocialManager(socials);

onMounted(async () => {
    emit('loading', true);

    try {
        await loadSocial();
    } finally {
        emit('loading', false);
    }
});
const addSocial = () => {
    socials.push({
        id: Date.now().toString(),
        name: '',
        url: '',
        icon: null,
        iconPreview: null,
        editing: true,
        isNew: true,
        mode: 'create'
    });
};

const editSocial = (social) => {
    social.original = {
        name: social.name,
        url: social.url,
        iconPreview: social.iconPreview
    };
    social.editing = true;
    social.mode = 'update';
};

const cancelEdit = (social, index) => {
    if (social.isNew) {
        socials.splice(index, 1);
        return;
    }

    social.name = social.original.name;
    social.url = social.original.url;
    social.iconPreview = social.original.iconPreview;
    social.icon = null;
    social.editing = false;
};

const handleIconUpload = (event, social) => {
    const file = event.target.files[0];
    if (!file) return;
    social.icon = file;
    social.iconPreview = URL.createObjectURL(file);
};
</script>

<template>
    <div class="space-y-5">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Footer Social</h2>
                <p class="mt-2 text-sm text-gray-500">Configure social media platforms, links, and icons shown in the
                    footer.</p>
            </div>
            <button @click="addSocial"
                class="flex items-center gap-2 rounded-lg bg-blue-600 px-2 py-1 text-white hover:bg-blue-700">
                <Plus :size="16" />
                Add Social
            </button>
        </div>

        <div v-if="!socials.length" class="rounded-xl border border-gray-100 bg-white p-10 text-center">
            <p class="text-lg text-gray-400">No social links available</p>
        </div>

        <div v-else>
            <div v-for="(social, index) in socials" :key="social.id"
                class="group mb-5 rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
                <div v-if="social.editing" class="space-y-4">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Social Name</label>
                            <input v-model="social.name" type="text" placeholder="Facebook"
                                class="w-full rounded-lg border border-gray-100 px-2 py-1 outline-none focus:border-gray-100" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Social URL</label>
                            <input v-model="social.url" type="text" placeholder="https://facebook.com"
                                class="w-full rounded-lg border border-gray-100 px-2 py-1 outline-none focus:border-gray-100" />
                        </div>
                    </div>

                    <div class="flex items-end justify-between">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Icon</label>

                            <label class="cursor-pointer">
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 hover:bg-gray-100">

                                    <img v-if="social.iconPreview" :src="social.iconPreview"
                                        class="h-12 w-12 object-contain" />

                                    <span v-else class="text-xs text-gray-400">
                                        No Icon
                                    </span>

                                </div>

                                <input type="file" accept="image/*" class="hidden"
                                    @change="handleIconUpload($event, social)" />
                            </label>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button @click="saveSocial(social)" :disabled="saving"
                                class="flex items-center gap-2 rounded-lg bg-blue-600 px-2 py-1 text-white disabled:opacity-50">
                                <Save :size="16" />
                                Save
                            </button>

                            <button @click="cancelEdit(social, index)"
                                class="flex items-center gap-2 rounded-lg bg-red-500 px-2 py-1 text-white">
                                <X :size="16" />
                                Cancel
                            </button>
                        </div>
                    </div>

                </div>

                <div v-else class="space-y-1">

                    <div class="flex items-end gap-4">
                        <div class="grid flex-1 grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500">Name</p>
                                <input :value="social.name" readonly
                                    class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1 font-medium bg-gray-50 outline-none focus:border-gray-100 focus:ring-0" />
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">URL</p>
                                <input :value="social.url" readonly
                                    class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1 font-medium bg-gray-50 outline-none focus:border-gray-100 focus:ring-0" />
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button v-if="!social.editing" @click="editSocial(social)"
                                class="rounded bg-blue-500 px-2 py-1 text-white">
                                <Edit :size="16" />
                            </button>

                            <button @click="confirmDelete(index)" class="rounded bg-red-500 px-2 py-1 text-white">
                                <Trash2 :size="16" />
                            </button>
                        </div>
                    </div>

                    <div>
                        <p class="mb-2 text-sm text-gray-500">
                            Icon
                        </p>

                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-lg border border-gray-200 bg-gray-50">

                            <img v-if="social.iconPreview" :src="social.iconPreview" class="h-12 w-12 object-contain" />

                            <span v-else class="text-xs text-gray-400">
                                No Icon
                            </span>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <ConfirmModal :show="showDeleteModal" title="Delete Social"
        message="Are you sure you want to delete this social link?" @confirm="deleteSocial" @cancel="cancelDelete" />
</template>