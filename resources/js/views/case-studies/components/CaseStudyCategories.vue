<script setup>
import { computed, ref } from 'vue';
import {
    Plus,
    Search,
    Edit3,
    Trash2,
    X,
    Save,
    PlusCircle
} from 'lucide-vue-next';
import { useToast } from 'vue-toastification';
import { useCaseStudy } from '@/composables/caseStudy/useCaseStudy';
import ConfirmModal from '@/components/common/ConfirmModal.vue';

const toast = useToast();

const { categories, loading, updateItem, removeItem } =
    useCaseStudy('categories');

const search = ref('');
const adding = ref(false);

const newCategory = ref({
    title: '',
    children: []
});

const newChild = ref('');

const editingId = ref(null);

const editForm = ref({
    id: '',
    title: '',
    children: []
});

const editNewChild = ref('');

const showDeleteModal = ref(false);
const deletingCategory = ref(null);

const clone = (value) => {
    if (value === undefined || value === null) {
        return value;
    }

    try {
        return JSON.parse(JSON.stringify(value));
    } catch (error) {
        return value;
    }
};

const getErrorMessage = (error, fallback) => {
    return (
        error?.graphQLErrors?.[0]?.message ||
        error?.networkError?.result?.errors?.[0]?.message ||
        error?.networkError?.result?.message ||
        error?.message ||
        fallback
    );
};

const createTempId = () => {
    if (
        typeof crypto !== 'undefined' &&
        typeof crypto.randomUUID === 'function'
    ) {
        return `new-${crypto.randomUUID()}`;
    }

    return `new-${Date.now()}-${Math.random().toString(36).substring(2, 10)}`;
};

const createTempChild = (name) => {
    return {
        id: createTempId(),
        name: String(name).trim()
    };
};

const normalizeChildrenForPayload = (children) => {
    if (!Array.isArray(children)) {
        return [];
    }

    return children
        .map((child) => {
            if (!child || typeof child !== 'object') {
                return null;
            }

            const name = String(child.name ?? '').trim();

            if (!name) {
                return null;
            }

            const item = {
                name
            };

            if (child.id && !String(child.id).startsWith('new-')) {
                item.id = String(child.id);
            }

            return item;
        })
        .filter(Boolean);
};

const resetNewCategory = () => {
    newCategory.value = {
        title: '',
        children: []
    };

    newChild.value = '';
};

const resetEditForm = () => {
    editForm.value = {
        id: '',
        title: '',
        children: []
    };

    editNewChild.value = '';
};

const filteredCategories = computed(() => {
    const keyword = search.value.trim().toLowerCase();

    if (!keyword) {
        return categories.value;
    }

    return categories.value.filter((category) => {
        const title = String(category?.title ?? '').toLowerCase();

        const children = Array.isArray(category?.children)
            ? category.children
            : [];

        const childMatched = children.some((child) => {
            return String(child?.name ?? '')
                .toLowerCase()
                .includes(keyword);
        });

        return title.includes(keyword) || childMatched;
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
            children: [],
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
    resetEditForm();
    resetNewCategory();
    adding.value = true;
};

const cancelAdd = () => {
    if (loading.value) {
        return;
    }

    adding.value = false;
    resetNewCategory();
};

const addNewChild = () => {
    const name = String(newChild.value ?? '').trim();

    if (!name) {
        toast.error('Please enter subcategory name.');
        return;
    }

    if (!Array.isArray(newCategory.value.children)) {
        newCategory.value.children = [];
    }

    const exists = newCategory.value.children.some((child) => {
        return (
            String(child?.name ?? '')
                .trim()
                .toLowerCase() === name.toLowerCase()
        );
    });

    if (exists) {
        toast.error('This subcategory already exists.');
        return;
    }

    newCategory.value.children.push(createTempChild(name));

    newChild.value = '';
};

const removeNewChild = (index) => {
    if (!Array.isArray(newCategory.value.children)) {
        return;
    }

    if (index < 0 || index >= newCategory.value.children.length) {
        return;
    }

    newCategory.value.children.splice(index, 1);
};

const saveNewCategory = async () => {
    try {
        const title = String(newCategory.value.title ?? '').trim();

        if (!title) {
            toast.error('Please enter category name.');
            return;
        }

        const exists = categories.value.some((category) => {
            return (
                String(category?.title ?? '')
                    .trim()
                    .toLowerCase() === title.toLowerCase()
            );
        });

        if (exists) {
            toast.error('This category already exists.');
            return;
        }

        const children = normalizeChildrenForPayload(
            newCategory.value.children
        );

        const payload = {
            title,
            children
        };

        await updateItem(payload);

        adding.value = false;
        resetNewCategory();
    } catch (error) {
        console.error('[CaseStudyCategories] Create error:', error);

        toast.error(getErrorMessage(error, 'Failed to create category.'));
    }
};

const startEdit = (category) => {
    if (!category?.id || adding.value || loading.value) {
        return;
    }

    const categoryId = String(category.id);

    const children = Array.isArray(category.children)
        ? category.children.map((child) => ({
              id: child?.id ? String(child.id) : createTempId(),
              name: String(child?.name ?? '')
          }))
        : [];

    editForm.value = {
        id: categoryId,
        title: String(category.title ?? ''),
        children
    };

    editingId.value = categoryId;
    editNewChild.value = '';
};

const cancelEdit = () => {
    if (loading.value) {
        return;
    }

    editingId.value = null;
    resetEditForm();
};

const addEditChild = () => {
    const name = String(editNewChild.value ?? '').trim();

    if (!name) {
        toast.error('Please enter subcategory name.');
        return;
    }

    const currentChildren = Array.isArray(editForm.value.children)
        ? editForm.value.children
        : [];

    const exists = currentChildren.some((child) => {
        return (
            String(child?.name ?? '')
                .trim()
                .toLowerCase() === name.toLowerCase()
        );
    });

    if (exists) {
        toast.error(`Subcategory "${name}" already exists.`);
        return;
    }

    editForm.value = {
        ...editForm.value,
        children: [
            ...currentChildren,
            {
                id: createTempId(),
                name
            }
        ]
    };

    editNewChild.value = '';
};

const removeEditChild = (index) => {
    if (!Array.isArray(editForm.value.children)) {
        return;
    }

    if (index < 0 || index >= editForm.value.children.length) {
        return;
    }

    editForm.value.children.splice(index, 1);
};

const saveEdit = async () => {
    try {
        const form = clone(editForm.value);

        if (!form?.id) {
            toast.error('Category ID is missing.');
            return;
        }

        const categoryId = String(form.id);

        const title = String(form.title ?? '').trim();

        if (!title) {
            toast.error('Please enter category name.');
            return;
        }

        const exists = categories.value.some((item) => {
            const itemId = String(item?.id ?? '');

            if (itemId === categoryId) {
                return false;
            }

            return (
                String(item?.title ?? '')
                    .trim()
                    .toLowerCase() === title.toLowerCase()
            );
        });

        if (exists) {
            toast.error('This category already exists.');
            return;
        }

        const children = normalizeChildrenForPayload(form.children);

        const payload = {
            id: categoryId,
            title,
            children
        };

        await updateItem(payload);

        editingId.value = null;
        resetEditForm();
    } catch (error) {
        console.error('[CaseStudyCategories] Update error:', error);

        toast.error(getErrorMessage(error, 'Failed to update category.'));
    }
};

const openDeleteModal = (category) => {
    if (!category?.id || loading.value) {
        return;
    }

    deletingCategory.value = clone(category);
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    if (loading.value) {
        return;
    }

    showDeleteModal.value = false;
    deletingCategory.value = null;
};

const confirmDelete = async () => {
    try {
        const id = deletingCategory.value?.id;

        if (!id) {
            closeDeleteModal();
            return;
        }

        await removeItem(String(id));

        showDeleteModal.value = false;
        deletingCategory.value = null;
    } catch (error) {
        console.error('[CaseStudyCategories] Delete error:', error);

        toast.error(getErrorMessage(error, 'Failed to delete category.'));
    }
};
</script>

<template>
    <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
        <div class="mb-7 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Case Study Category Management
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Manage categories and subcategories displayed in the Case
                    Study filter.
                </p>
            </div>

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
                    placeholder="Search category or subcategory..."
                    class="w-full rounded-xl border border-slate-200 py-3.5 pl-12 pr-4 text-base text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                />
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200">
            <div class="max-h-[600px] overflow-y-auto">
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
                                Category
                            </th>

                            <th
                                class="border-r border-blue-400 px-6 py-4 text-center text-sm font-semibold"
                            >
                                Subcategories
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
                                <td
                                    class="border-r border-slate-200 px-5 py-4 align-top"
                                >
                                    <input
                                        v-model="newCategory.title"
                                        type="text"
                                        placeholder="Category name"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    />
                                </td>

                                <td class="border-r border-slate-200 px-5 py-4">
                                    <div class="space-y-3">
                                        <div
                                            v-if="newCategory.children.length"
                                            class="space-y-2"
                                        >
                                            <div
                                                v-for="(
                                                    child, childIndex
                                                ) in newCategory.children"
                                                :key="child.id"
                                                class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white p-2"
                                            >
                                                <input
                                                    v-model="child.name"
                                                    type="text"
                                                    placeholder="Subcategory name"
                                                    class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 outline-none focus:border-blue-500"
                                                />

                                                <button
                                                    type="button"
                                                    @click="
                                                        removeNewChild(
                                                            childIndex
                                                        )
                                                    "
                                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-red-500 transition hover:bg-red-50"
                                                >
                                                    <Trash2 :size="17" />
                                                </button>
                                            </div>
                                        </div>

                                        <div class="flex gap-2">
                                            <input
                                                v-model="newChild"
                                                type="text"
                                                placeholder="Add subcategory..."
                                                class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                                @keyup.enter.prevent="
                                                    addNewChild
                                                "
                                            />

                                            <button
                                                type="button"
                                                @click="addNewChild"
                                                class="inline-flex shrink-0 items-center gap-1 rounded-lg bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-600 transition hover:bg-blue-100"
                                            >
                                                <PlusCircle :size="17" />
                                                Add
                                            </button>
                                        </div>
                                    </div>
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
                                        >
                                            <Save :size="18" />
                                        </button>

                                        <button
                                            type="button"
                                            @click="cancelAdd"
                                            :disabled="loading"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-500 text-white transition hover:bg-slate-600 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <X :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </template>

                            <template
                                v-else-if="editingId === String(category.id)"
                            >
                                <td
                                    class="border-r border-slate-200 px-5 py-4 align-top"
                                >
                                    <input
                                        v-model="editForm.title"
                                        type="text"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    />
                                </td>

                                <td class="border-r border-slate-200 px-5 py-4">
                                    <div class="space-y-3">
                                        <div
                                            v-if="editForm.children.length"
                                            class="space-y-2"
                                        >
                                            <div
                                                v-for="(
                                                    child, childIndex
                                                ) in editForm.children"
                                                :key="`${child.id || 'child'}-${childIndex}`"
                                                class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white p-2"
                                            >
                                                <input
                                                    v-model="child.name"
                                                    type="text"
                                                    placeholder="Subcategory name"
                                                    class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 outline-none focus:border-blue-500"
                                                />

                                                <button
                                                    type="button"
                                                    @click="
                                                        removeEditChild(
                                                            childIndex
                                                        )
                                                    "
                                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-red-500 transition hover:bg-red-50"
                                                >
                                                    <Trash2 :size="17" />
                                                </button>
                                            </div>
                                        </div>

                                        <div class="flex gap-2">
                                            <input
                                                v-model="editNewChild"
                                                type="text"
                                                placeholder="Add subcategory..."
                                                class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                                @keyup.enter.prevent="
                                                    addEditChild
                                                "
                                            />

                                            <button
                                                type="button"
                                                @click.prevent.stop="
                                                    addEditChild
                                                "
                                                class="inline-flex shrink-0 items-center gap-1 rounded-lg bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-600 transition hover:bg-blue-100"
                                            >
                                                <PlusCircle :size="17" />
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center justify-center gap-3"
                                    >
                                        <button
                                            type="button"
                                            @click="saveEdit"
                                            :disabled="loading"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <Save :size="18" />
                                        </button>

                                        <button
                                            type="button"
                                            @click="cancelEdit"
                                            :disabled="loading"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-500 text-white transition hover:bg-slate-600 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <X :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </template>

                            <template v-else>
                                <td
                                    class="border-r border-slate-200 px-6 py-5 align-top text-center"
                                >
                                    <div class="font-semibold text-slate-800">
                                        {{ category.title || '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ category.children?.length || 0 }}
                                        subcategories
                                    </div>
                                </td>

                                <td class="border-r border-slate-200 px-6 py-5">
                                    <div
                                        v-if="category.children?.length"
                                        class="space-y-2"
                                    >
                                        <div
                                            v-for="(
                                                child, childIndex
                                            ) in category.children"
                                            :key="`${child.id || 'child'}-${childIndex}`"
                                            class="flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700"
                                        >
                                            <span
                                                class="h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500"
                                            ></span>

                                            <span>
                                                {{ child.name || '-' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div v-else class="text-sm text-slate-400">
                                        No subcategories
                                    </div>
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
                                        >
                                            <Edit3 :size="18" />
                                        </button>

                                        <button
                                            type="button"
                                            @click="openDeleteModal(category)"
                                            :disabled="adding || loading"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-500 text-white transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <Trash2 :size="18" />
                                        </button>
                                    </div>
                                </td>
                            </template>
                        </tr>
                        <tr
                            v-if="
                                !loading && displayCategories.length === 0
                            "
                        >
                            <td
                                colspan="3"
                                class="px-6 py-12 text-center text-sm text-slate-400"
                            >
                                No case study categories found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Case Study Category"
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
