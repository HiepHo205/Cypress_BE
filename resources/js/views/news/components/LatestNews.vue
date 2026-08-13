<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { Upload, Save, X, Edit3, Trash2, Plus } from 'lucide-vue-next';
import { useNews } from '../../../composables/news/useNews';
import { useToast } from 'vue-toastification';
import ConfirmModal from '../../../components/common/ConfirmModal.vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    }
});

const { updateItem, removeItem, news } = useNews('latest');

const toast = useToast();

const forms = ref([]);
const editing = ref([]);
const saving = ref(false);
const deleting = ref(false);
const adding = ref(false);

const currentPage = ref(1);
const perPage = 6;

const showDeleteModal = ref(false);
const deleteTarget = ref(null);
const deleteTargetIndex = ref(null);

const createForm = (item = {}) => ({
    id: item.id ?? null,
    title: item.title ?? '',
    description: item.description ?? '',
    date: item.date ?? '',
    category: item.category ?? '',
    imageFile: null,
    imagePreview: item.image?.url ?? item.image ?? '',
    featured: item.featured ?? false,
    isNew: item.id == null
});

const categories = computed(() => {
    const value = news.value?.postCategories;

    console.log('News page:', news.value);
    console.log('Raw postCategories:', value);

    if (!Array.isArray(value)) {
        console.log('postCategories is not an array:', value);
        return [];
    }

    return value;
});

const categoryOptions = computed(() => {
    const options = categories.value
        .map((category) => {
            if (typeof category === 'string') {
                return category;
            }

            return (
                category?.title ?? category?.name ?? category?.category ?? ''
            );
        })
        .filter(Boolean);

    const uniqueOptions = [...new Set(options)];

    console.log('Category options:', uniqueOptions);

    return uniqueOptions;
});

const totalPages = computed(() => {
    const total = Math.ceil(forms.value.length / perPage);

    return Math.max(1, total);
});

const paginatedForms = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    const end = start + perPage;

    const result = forms.value.slice(start, end).map((form, index) => ({
        form,
        index: start + index
    }));

    console.log('Current page:', currentPage.value);
    console.log('Paginated forms:', result);

    return result;
});

const pageNumbers = computed(() => {
    const total = totalPages.value;
    const current = currentPage.value;

    if (total <= 5) {
        return Array.from({ length: total }, (_, index) => index + 1);
    }

    if (current <= 3) {
        return [1, 2, 3, 4, 5];
    }

    if (current >= total - 2) {
        return [total - 4, total - 3, total - 2, total - 1, total];
    }

    return [current - 2, current - 1, current, current + 1, current + 2];
});

const saveButtonText = computed(() => 'Save');

watch(
    () => news.value?.postCategories,
    (value) => {
        console.log('================ CATEGORY DEBUG ================');
        console.log('News page:', news.value);
        console.log('postCategories changed:', value);
        console.log('Is array:', Array.isArray(value));
        console.log('Category options:', categoryOptions.value);
        console.log('=================================================');
    },
    {
        immediate: true,
        deep: true
    }
);

watch(
    () => props.items,
    (items) => {
        console.log('================ NEWS ITEMS DEBUG ================');
        console.log('Latest news items:', items);
        console.log(
            'Categories from newsPage.postCategories:',
            news.value?.postCategories
        );
        console.log('Available category options:', categoryOptions.value);
        console.log('===================================================');

        const oldForms = forms.value;
        const oldEditing = editing.value;

        forms.value = items.map((item) => {
            const oldIndex = oldForms.findIndex((form) => form.id === item.id);

            const existingForm = oldIndex >= 0 ? oldForms[oldIndex] : null;

            if (!existingForm) {
                return createForm(item);
            }

            return {
                ...existingForm,
                ...createForm(item),
                imageFile: existingForm.imageFile,
                imagePreview: existingForm.imageFile
                    ? existingForm.imagePreview
                    : (item.image?.url ?? item.image ?? '')
            };
        });

        editing.value = forms.value.map((form) => {
            const oldIndex = oldForms.findIndex(
                (oldForm) => oldForm.id === form.id
            );

            return oldIndex >= 0
                ? Boolean(oldEditing[oldIndex])
                : Boolean(form.isNew);
        });

        if (currentPage.value > totalPages.value) {
            currentPage.value = totalPages.value;
        }
    },
    {
        immediate: true,
        deep: true
    }
);

watch(
    categoryOptions,
    (value) => {
        console.log('Category options changed:', value);
        console.log('Category count:', value.length);
    },
    {
        immediate: true,
        deep: true
    }
);

const goToPage = (page) => {
    if (saving.value || deleting.value || adding.value) {
        return;
    }

    if (page < 1 || page > totalPages.value) {
        return;
    }

    const editingIndex = editing.value.findIndex(Boolean);

    if (editingIndex !== -1) {
        cancel(editingIndex);
    }

    currentPage.value = page;

    console.log('Go to page:', page);

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

const previousPage = () => {
    if (currentPage.value > 1) {
        goToPage(currentPage.value - 1);
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        goToPage(currentPage.value + 1);
    }
};

const handleImageUpload = (event, index) => {
    const file = event.target.files?.[0];

    if (!file || !forms.value[index]) {
        return;
    }

    if (forms.value[index].imagePreview?.startsWith('blob:')) {
        URL.revokeObjectURL(forms.value[index].imagePreview);
    }

    forms.value[index].imageFile = file;

    forms.value[index].imagePreview = URL.createObjectURL(file);

    console.log('Selected image:', file);
};

const addNews = () => {
    if (saving.value || deleting.value || adding.value) {
        return;
    }

    const editingIndex = editing.value.findIndex(Boolean);

    if (editingIndex !== -1) {
        cancel(editingIndex);
    }

    console.log('Categories before creating news:', categories.value);

    console.log(
        'Category options before creating news:',
        categoryOptions.value
    );

    const defaultCategory =
        categoryOptions.value.length > 0 ? categoryOptions.value[0] : '';

    console.log('Default category:', defaultCategory);

    const newForm = createForm({
        title: '',
        description: '',
        date: new Date().toISOString().slice(0, 10),
        category: defaultCategory,
        featured: false
    });

    forms.value.unshift(newForm);
    editing.value.unshift(true);

    currentPage.value = 1;

    adding.value = true;

    requestAnimationFrame(() => {
        adding.value = false;
    });

    console.log('New news form:', newForm);
};

const startEdit = (index) => {
    if (saving.value || deleting.value || adding.value) {
        return;
    }

    const currentIndex = editing.value.findIndex(Boolean);

    if (currentIndex !== -1 && currentIndex !== index) {
        cancel(currentIndex);
    }

    editing.value[index] = true;

    console.log('Editing item:', forms.value[index]);

    console.log('Current category options:', categoryOptions.value);

    console.log('Current selected category:', forms.value[index]?.category);
};

const cancel = (index) => {
    const form = forms.value[index];

    if (!form) {
        return;
    }

    if (form.isNew) {
        if (form.imagePreview?.startsWith('blob:')) {
            URL.revokeObjectURL(form.imagePreview);
        }

        forms.value.splice(index, 1);
        editing.value.splice(index, 1);

        if (currentPage.value > totalPages.value) {
            currentPage.value = totalPages.value;
        }

        return;
    }

    if (form.id) {
        const original = props.items.find((item) => item.id === form.id);

        if (original) {
            forms.value[index] = createForm(original);
        }
    }

    editing.value[index] = false;
};

const saveNews = async (index) => {
    const form = forms.value[index];

    if (!form) {
        return;
    }

    if (!form.title?.trim()) {
        toast.error('Please enter a title.');
        return;
    }

    if (!form.date) {
        toast.error('Please select a date.');
        return;
    }

    if (!form.category) {
        toast.error('Please select a category.');
        return;
    }

    const payload = {
        id: form.id,
        title: form.title.trim(),
        description: form.description?.trim() || '',
        date: form.date,
        category: form.category,
        featured: Boolean(form.featured)
    };

    console.log('================ SAVE NEWS ================');

    console.log('Saving news payload:', payload);

    console.log('Selected category:', form.category);

    console.log('Available categories:', categoryOptions.value);

    console.log('Category objects:', categories.value);

    console.log('============================================');

    saving.value = true;

    try {
        await updateItem(payload, form.imageFile);

        editing.value[index] = false;

        if (form.imagePreview?.startsWith('blob:')) {
            URL.revokeObjectURL(form.imagePreview);
        }

        form.imageFile = null;
    } catch (error) {
        console.error('Save news error:', error);
    } finally {
        saving.value = false;
    }
};

const openDeleteModal = (item, index) => {
    if (!item || item.isNew || !item.id) {
        return;
    }

    if (saving.value || deleting.value) {
        return;
    }

    deleteTarget.value = item;
    deleteTargetIndex.value = index;
    showDeleteModal.value = true;

    console.log('Delete target:', item);
};

const cancelDelete = () => {
    if (deleting.value) {
        return;
    }

    showDeleteModal.value = false;
    deleteTarget.value = null;
    deleteTargetIndex.value = null;
};

const confirmDelete = async () => {
    const item = deleteTarget.value;

    if (!item?.id) {
        cancelDelete();
        return;
    }

    const itemId = item.id;

    showDeleteModal.value = false;
    deleteTarget.value = null;
    deleteTargetIndex.value = null;

    deleting.value = true;

    console.log('Deleting news ID:', itemId);

    try {
        await removeItem(itemId);

        if (currentPage.value > totalPages.value) {
            currentPage.value = totalPages.value;
        }
    } catch (error) {
        console.error('Delete news error:', error);
    } finally {
        deleting.value = false;
    }
};

onBeforeUnmount(() => {
    forms.value.forEach((form) => {
        if (form.imagePreview?.startsWith('blob:')) {
            URL.revokeObjectURL(form.imagePreview);
        }
    });
});
</script>

<template>
    <div>
        <div class="mt-7 mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">
                    Latest News
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Manage your latest news articles.
                </p>
            </div>

            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-[#3674d9] px-3 py-2 text-xs font-medium text-white transition hover:bg-[#2863c5] disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="saving || deleting || adding"
                @click="addNews"
            >
                <Plus :size="15" />
                Add Latest News
            </button>
        </div>

        <div
            v-if="paginatedForms.length"
            class="grid grid-cols-1 gap-4 md:grid-cols-2"
        >
            <form
                v-for="item in paginatedForms"
                :key="item.form.id ?? `new-${item.index}`"
                class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white"
                @submit.prevent="saveNews(item.index)"
            >
                <div class="p-4">
                    <div class="flex gap-4">
                        <label
                            class="group relative flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-100"
                            :class="
                                editing[item.index]
                                    ? 'cursor-pointer'
                                    : 'cursor-default'
                            "
                        >
                            <img
                                v-if="item.form.imagePreview"
                                :src="item.form.imagePreview"
                                alt="Preview"
                                class="h-full w-full object-cover"
                            />

                            <div
                                v-else
                                class="flex h-full w-full flex-col items-center justify-center text-slate-400"
                            >
                                <Upload :size="22" />

                                <span class="mt-1 text-center text-[10px]">
                                    Upload
                                </span>
                            </div>

                            <div
                                v-if="editing[item.index]"
                                class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition group-hover:opacity-100"
                            >
                                <Upload :size="20" class="text-white" />
                            </div>

                            <input
                                v-if="editing[item.index]"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleImageUpload($event, item.index)"
                            />
                        </label>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <input
                                        v-if="editing[item.index]"
                                        v-model="item.form.title"
                                        type="text"
                                        required
                                        placeholder="Enter news title"
                                        class="w-full rounded-md border border-slate-200 px-2 py-1 text-sm font-semibold text-slate-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                    />

                                    <h3
                                        v-else
                                        class="truncate text-sm font-semibold text-slate-800"
                                    >
                                        {{ item.form.title || 'Untitled' }}
                                    </h3>

                                    <textarea
                                        v-if="editing[item.index]"
                                        v-model="item.form.description"
                                        rows="2"
                                        maxlength="200"
                                        placeholder="Enter description"
                                        class="mt-2 w-full resize-none rounded-md border border-slate-200 px-2 py-1 text-xs leading-4 text-slate-500 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                    ></textarea>

                                    <p
                                        v-else
                                        class="mt-2 line-clamp-2 text-xs leading-4 text-slate-500"
                                    >
                                        {{
                                            item.form.description ||
                                            'No description'
                                        }}
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-1">
                                    <button
                                        v-if="!item.form.isNew"
                                        type="button"
                                        class="rounded-md p-1.5 text-blue-600 transition hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        :disabled="
                                            editing[item.index] ||
                                            saving ||
                                            deleting
                                        "
                                        @click="startEdit(item.index)"
                                    >
                                        <Edit3 :size="16" />
                                    </button>

                                    <button
                                        v-if="!item.form.isNew"
                                        type="button"
                                        class="rounded-md p-1.5 text-red-500 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        :disabled="
                                            editing[item.index] ||
                                            saving ||
                                            deleting
                                        "
                                        @click="
                                            openDeleteModal(
                                                item.form,
                                                item.index
                                            )
                                        "
                                    >
                                        <Trash2 :size="16" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 border-t border-slate-100">
                    <div class="px-4 py-3">
                        <label
                            class="mb-1 block text-[10px] font-medium uppercase tracking-wide text-slate-400"
                        >
                            Category
                        </label>

                        <select
                            v-if="editing[item.index]"
                            v-model="item.form.category"
                            class="w-full rounded-md border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                            :class="
                                item.form.category
                                    ? 'text-slate-600'
                                    : 'text-slate-400'
                            "
                        >
                            <option value="" disabled>Select category</option>

                            <option
                                v-for="category in categoryOptions"
                                :key="category"
                                :value="category"
                                class="text-slate-600"
                            >
                                {{ category }}
                            </option>
                        </select>

                        <p v-else class="text-xs text-slate-600">
                            {{ item.form.category || 'No category' }}
                        </p>

                        <p
                            v-if="
                                editing[item.index] &&
                                categoryOptions.length === 0
                            "
                            class="mt-1 text-[10px] text-red-500"
                        >
                            No categories found
                        </p>
                    </div>

                    <div class="border-l border-slate-100 px-4 py-3">
                        <label
                            class="mb-1 block text-[10px] font-medium uppercase tracking-wide text-slate-400"
                        >
                            Date
                        </label>

                        <input
                            v-if="editing[item.index]"
                            v-model="item.form.date"
                            type="date"
                            class="w-full rounded-md border border-slate-200 px-2 py-1 text-xs text-slate-600 outline-none focus:border-blue-400"
                        />

                        <p v-else class="text-xs text-slate-600">
                            {{ item.form.date }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="editing[item.index]"
                    class="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50/50 px-4 py-3"
                >
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="saving"
                        @click="cancel(item.index)"
                    >
                        <X :size="14" />
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-1 rounded-lg bg-[#3674d9] px-3 py-1.5 text-xs font-medium text-white transition hover:bg-[#2863c5] disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="saving"
                    >
                        <Save :size="14" />

                        {{
                            saving
                                ? 'Saving...'
                                : item.form.isNew
                                  ? 'Create'
                                  : saveButtonText
                        }}
                    </button>
                </div>
            </form>
        </div>

        <div
            v-else
            class="rounded-xl border border-dashed border-slate-300 py-10 text-center text-sm text-slate-500"
        >
            <p>No content found.</p>
        </div>

        <div
            v-if="forms.length > 0"
            class="mt-6 flex items-center justify-center border-t border-slate-100 pt-4"
        >
            <div class="flex items-center justify-center gap-1">
                <button
                    type="button"
                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="currentPage === 1 || saving || deleting"
                    @click="previousPage"
                >
                    <span class="text-sm">‹</span>
                </button>

                <button
                    v-for="page in pageNumbers"
                    :key="page"
                    type="button"
                    class="flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-xs font-medium transition"
                    :class="
                        currentPage === page
                            ? 'bg-[#3674d9] text-white'
                            : 'border border-slate-200 text-slate-600 hover:bg-slate-50'
                    "
                    :disabled="saving || deleting"
                    @click="goToPage(page)"
                >
                    {{ page }}
                </button>

                <button
                    type="button"
                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="currentPage === totalPages || saving || deleting"
                    @click="nextPage"
                >
                    <span class="text-sm">›</span>
                </button>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Latest News"
            :message="
                deleteTarget?.title
                    ? `Are you sure you want to delete &quot;${deleteTarget.title}&quot;?`
                    : 'Are you sure you want to delete this news item?'
            "
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </div>
</template>
