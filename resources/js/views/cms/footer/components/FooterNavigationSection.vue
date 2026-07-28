<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import { ArrowLeft, Edit, Trash2, X } from 'lucide-vue-next';
import ConfirmModal from '@/components/common/ConfirmModal.vue';
import useFooterNavigationManager from '@/composables/footer/useFooterNavigationManager';
const emit = defineEmits(['loading']);
const toast = useToast();

const {
    navigationGroups,
    selectedGroup,
    saving,
    pageLoading,
    editing,
    isNewGroup,
    loadNavigation,
    addGroup,
    openGroup,
    addItem,
    removeItem,
    removeGroup,
    saveGroup
} = useFooterNavigationManager();

const showDeleteModal = ref(false);
const deleteGroupIndex = ref(null);

onMounted(async () => {

    emit('loading', true);

    try {

        await loadNavigation();

    } catch (error) {

        toast.error('Failed to load navigation');

    } finally {

        pageLoading.value = false;

        emit('loading', false);

    }

});

const confirmDeleteGroup = (index) => {
    deleteGroupIndex.value = index;
    showDeleteModal.value = true;
};

const cancelDeleteGroup = () => {
    showDeleteModal.value = false;
    deleteGroupIndex.value = null;
};

const deleteGroup = async () => {
    if (deleteGroupIndex.value === null) return;

    const index = deleteGroupIndex.value;
    const group = navigationGroups[index];

    cancelDeleteGroup();

    const loadingToast = toast.info('Deleting navigation...', {
        timeout: false
    });

    try {
        await removeGroup(group);

        toast.dismiss(loadingToast);
        toast.success('Navigation deleted successfully.');
    } catch (error) {
        toast.dismiss(loadingToast);
        toast.error(error.message || 'Failed to delete navigation.');
    }
};
const handleSave = async () => {
    const loadingToast = toast.info(
        isNewGroup.value
            ? 'Creating navigation...'
            : 'Updating navigation...',
        {
            timeout: false
        }
    );

    try {
        const action = await saveGroup();

        toast.dismiss(loadingToast);

        toast.success(
            action === 'create'
                ? 'Navigation created successfully.'
                : 'Navigation updated successfully.'
        );
    } catch (error) {
        toast.dismiss(loadingToast);

        toast.error(error.message || 'Operation failed.');
    }
};
</script>

<template>
    <div class="space-y-8">
        <div v-if="!selectedGroup">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Footer Navigation
                    </h2>

                    <p class="mt-2 text-sm text-gray-500">
                        Configure footer navigation menus.
                    </p>
                </div>

                <button @click="addGroup"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700">
                    + Add Group
                </button>
            </div>

            <div class="mt-6">
                <div v-if="!navigationGroups.length"
                    class="rounded-xl border border-gray-100 bg-white p-10 text-center">
                    <p class="text-lg text-gray-400">
                        No navigation groups available
                    </p>
                </div>

                <div v-else class="grid grid-cols-3 gap-5">
                    <div v-for="(group, index) in navigationGroups" :key="index" @click="openGroup(group)"
                        class="group relative cursor-pointer rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-blue-500 hover:shadow-md">

                        <button @click.stop="confirmDeleteGroup(index)"
                            class="absolute right-3 top-3 hidden rounded-lg p-2 text-red-500 transition hover:bg-red-50 group-hover:block">
                            <Trash2 :size="16" />
                        </button>

                        <h3 class="font-semibold text-gray-900">
                            {{ group.name }}
                        </h3>

                        <p class="mt-3 text-sm text-gray-500">
                            {{ group.items.length }} Navigation
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <button @click="
                selectedGroup = null;
            editing = false;
            isNewGroup = false;
            " class="mb-5 flex items-center gap-2 text-sm text-blue-600 transition hover:text-blue-700">
                <ArrowLeft :size="16" />
                Back
            </button>

            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-md">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <input v-model="selectedGroup.name" :disabled="!editing"
                            class="rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 font-semibold text-blue-700 outline-none transition disabled:cursor-not-allowed disabled:opacity-60 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    </div>

                    <div v-if="!isNewGroup" class="flex gap-4">

                        <button @click="editing = !editing"
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-500 text-white transition hover:bg-blue-600">
                            <Edit :size="16" />
                        </button>

                        <button @click="confirmDeleteGroup(navigationGroups.findIndex(g => g === selectedGroup))"
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white transition hover:bg-red-600">
                            <X :size="16" />
                        </button>
                    </div>
                </div>
                <div class="space-y-4">
                    <div v-for="(item, index) in selectedGroup.items" :key="index"
                        class="grid grid-cols-12 gap-4 rounded-lg border border-gray-100 bg-gray-50 p-4">

                        <div class="col-span-6">
                            <label class="mb-2 block text-sm font-medium">
                                Navigation
                            </label>

                            <input v-model="item.title" :disabled="!editing" placeholder="About us"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 outline-none transition disabled:cursor-not-allowed disabled:bg-gray-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                        </div>

                        <div class="col-span-5">
                            <label class="mb-2 block text-sm font-medium">
                                Link
                            </label>

                            <input v-model="item.link" :disabled="!editing" placeholder="/about"
                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 outline-none transition disabled:cursor-not-allowed disabled:bg-gray-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                        </div>

                        <div class="col-span-1 flex items-end justify-end">
                            <button v-if="editing" @click="removeItem(index)"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100">
                                <Trash2 :size="16" />
                            </button>
                        </div>
                    </div>

                    <button @click="addItem(selectedGroup)" :disabled="!editing"
                        class="rounded-lg border border-blue-100 px-3 py-2 text-blue-600 transition hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-50">
                        + Add Navigation
                    </button>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="handleSave" :disabled="saving || !editing"
                        class="rounded-lg bg-blue-600 px-5 py-2 text-white transition hover:bg-blue-700 disabled:opacity-50">
                        {{ saving ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <ConfirmModal :show="showDeleteModal" title="Delete Navigation Group"
        message="Are you sure you want to delete this navigation group?" @confirm="deleteGroup"
        @cancel="cancelDeleteGroup" />
</template>