<script setup>
import { computed, ref } from 'vue';
import { Plus, Search, Edit3, Trash2, X, Save } from 'lucide-vue-next';
import { useToast } from 'vue-toastification';
import { useNews } from '@/composables/news/useNews';
import ConfirmModal from '@/components/common/ConfirmModal.vue';

const toast = useToast();

const { news, loading, updateItem, removeItem } = useNews('categories');

const categories = computed(() => {
    return news.value?.postCategories ?? [];
});

const search = ref('');
const adding = ref(false);

const newCategory = ref({
    title: '',
    description: ''
});

const editingId = ref(null);

const editForm = ref({
    title: '',
    description: ''
});

const showDeleteModal = ref(false);
const deletingCategory = ref(null);

const filteredCategories = computed(() => {
    const keyword = search.value.trim().toLowerCase();

    if (!keyword) {
        return categories.value;
    }

    return categories.value.filter((category) => {
        const title = String(category.title ?? '').toLowerCase();
        const description = String(category.description ?? '').toLowerCase();

        return title.includes(keyword) || description.includes(keyword);
    });
});

const displayCategories = computed(() => {
    if (!adding.value) {
        return filteredCategories.value;
    }

    return [
        {
            id: '__new__',
            title: '',
            description: '',
            isNew: true
        },
        ...filteredCategories.value
    ];
});

const openAddRow = () => {
    if (adding.value || loading.value) {
        return;
    }

    editingId.value = null;

    newCategory.value = {
        title: '',
        description: ''
    };

    adding.value = true;
};

const cancelAdd = () => {
    adding.value = false;

    newCategory.value = {
        title: '',
        description: ''
    };
};

const saveNewCategory = async () => {
    const title = newCategory.value.title.trim();
    const description = newCategory.value.description.trim();

    if (!title) {
        toast.error('Please enter category name.');
        return;
    }

    if (!description) {
        toast.error('Please enter category description.');
        return;
    }

    try {
        await updateItem({
            title,
            description
        });

        cancelAdd();
    } catch (error) {
        toast.error(error?.message || 'Failed to create category.');
    }
};

const startEdit = (category) => {
    if (adding.value || loading.value) {
        return;
    }

    editingId.value = category.id;

    editForm.value = {
        title: category.title ?? '',
        description: category.description ?? ''
    };
};

const cancelEdit = () => {
    editingId.value = null;

    editForm.value = {
        title: '',
        description: ''
    };
};

const saveEdit = async (category) => {
    const title = editForm.value.title.trim();
    const description = editForm.value.description.trim();

    if (!title) {
        toast.error('Please enter category name.');
        return;
    }

    if (!description) {
        toast.error('Please enter category description.');
        return;
    }

    try {
        await updateItem({
            id: category.id,
            title,
            description
        });

        cancelEdit();
    } catch (error) {
        toast.error(error?.message || 'Failed to update category.');
    }
};

const openDeleteModal = (category) => {
    if (!category?.id || loading.value) {
        return;
    }

    deletingCategory.value = category;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deletingCategory.value = null;
};

const confirmDelete = async () => {
    if (!deletingCategory.value?.id) {
        closeDeleteModal();
        return;
    }

    const categoryId = String(deletingCategory.value.id);
    const categoryName = deletingCategory.value.title || 'this category';

    showDeleteModal.value = false;
    deletingCategory.value = null;

    try {
        await removeItem(categoryId);

        toast.success(`"${categoryName}" deleted successfully.`);
    } catch (error) {
        toast.error(error?.message || 'Failed to delete category.');
    }
};
</script>

<template>
    <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
        <div class="mb-7 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900">
                Post Category Management
            </h1>

            <button
                type="button"
                @click="openAddRow"
                :disabled="adding || loading"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-base font-medium text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <Plus :size="20" />
                Add Category
            </button>
        </div>

        <div class="mb-6">
            <div class="relative">
                <Search
                    :size="20"
                    class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                />

                <input
                    v-model="search"
                    type="text"
                    placeholder="Search category..."
                    class="w-full rounded-xl border border-slate-200 py-3.5 pl-12 pr-4 text-base text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200">
            <div class="max-h-[500px] overflow-y-auto">
                <table class="w-full table-fixed border-collapse">
                    <colgroup>
                        <col class="w-1/4" />
                        <col class="w-1/2" />
                        <col class="w-1/4" />
                    </colgroup>

                    <thead class="sticky top-0 z-10">
                        <tr class="bg-blue-600 text-white">
                            <th
                                class="border-r border-blue-400 px-6 py-4 text-center text-sm font-semibold"
                            >
                                Name
                            </th>

                            <th
                                class="border-r border-blue-400 px-6 py-4 text-center text-sm font-semibold"
                            >
                                Description
                            </th>

                            <th
                                class="px-6 py-4 text-center text-sm font-semibold"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="category in displayCategories"
                            :key="category.id"
                            class="border-b border-slate-200 bg-white"
                            :class="
                                category.isNew
                                    ? 'bg-blue-50/40'
                                    : 'hover:bg-slate-50'
                            "
                        >
                            <template v-if="category.isNew">
                                <td class="border-r border-slate-200 px-5 py-4">
                                    <input
                                        v-model="newCategory.title"
                                        type="text"
                                        placeholder="Category name"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        @keyup.enter="saveNewCategory"
                                    />
                                </td>

                                <td class="border-r border-slate-200 px-5 py-4">
                                    <input
                                        v-model="newCategory.description"
                                        type="text"
                                        placeholder="Category description"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        @keyup.enter="saveNewCategory"
                                    />
                                </td>

                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center justify-center gap-3"
                                    >
                                        <button
                                            type="button"
                                            @click="saveNewCategory"
                                            :disabled="loading"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            title="Save"
                                        >
                                            <Save :size="18" />
                                        </button>

                                        <button
                                            type="button"
                                            @click="cancelAdd"
                                            :disabled="loading"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-500 text-white transition hover:bg-slate-600 disabled:cursor-not-allowed disabled:opacity-50"
                                            title="Cancel"
                                        >
                                            <X :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </template>

                            <template v-else-if="editingId === category.id">
                                <td class="border-r border-slate-200 px-5 py-4">
                                    <input
                                        v-model="editForm.title"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        @keyup.enter="saveEdit(category)"
                                    />
                                </td>

                                <td class="border-r border-slate-200 px-5 py-4">
                                    <input
                                        v-model="editForm.description"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        @keyup.enter="saveEdit(category)"
                                    />
                                </td>

                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center justify-center gap-3"
                                    >
                                        <button
                                            type="button"
                                            @click="saveEdit(category)"
                                            :disabled="loading"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            title="Save"
                                        >
                                            <Save :size="18" />
                                        </button>

                                        <button
                                            type="button"
                                            @click="cancelEdit"
                                            :disabled="loading"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-500 text-white transition hover:bg-slate-600 disabled:cursor-not-allowed disabled:opacity-50"
                                            title="Cancel"
                                        >
                                            <X :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </template>

                            <template v-else>
                                <td
                                    class="border-r border-slate-200 px-6 py-5 text-center text-sm text-slate-800"
                                >
                                    {{ category.title || '-' }}
                                </td>

                                <td
                                    class="border-r border-slate-200 px-6 py-5 text-center text-sm text-slate-800"
                                >
                                    {{ category.description || '-' }}
                                </td>

                                <td class="px-6 py-5">
                                    <div
                                        class="flex items-center justify-center gap-3"
                                    >
                                        <button
                                            type="button"
                                            @click="startEdit(category)"
                                            :disabled="adding || loading"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            title="Edit"
                                        >
                                            <Edit3 :size="18" />
                                        </button>

                                        <button
                                            type="button"
                                            @click="openDeleteModal(category)"
                                            :disabled="adding || loading"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-500 text-white transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50"
                                            title="Delete"
                                        >
                                            <Trash2 :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </template>
                        </tr>

                        <tr v-if="loading && !adding">
                            <td
                                colspan="3"
                                class="px-6 py-12 text-center text-sm text-slate-400"
                            >
                                Loading categories...
                            </td>
                        </tr>

                        <tr
                            v-else-if="
                                !loading && displayCategories.length === 0
                            "
                        >
                            <td
                                colspan="3"
                                class="px-6 py-12 text-center text-sm text-slate-400"
                            >
                                No categories found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Category"
            :message="
                deletingCategory?.title
                    ? `Are you sure you want to delete &quot;${deletingCategory.title}&quot;?`
                    : 'Are you sure you want to delete this category?'
            "
            @confirm="confirmDelete"
            @cancel="closeDeleteModal"
        />
    </div>
</template>
