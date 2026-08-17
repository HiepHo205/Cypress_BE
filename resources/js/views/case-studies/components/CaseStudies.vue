<script setup>
import { ref, computed, watch } from 'vue';
import { Save, Upload, X, Edit3, Trash2, Plus } from 'lucide-vue-next';
import { useCaseStudy } from '@/composables/caseStudy/useCaseStudy';
import { useToast } from 'vue-toastification';
import ConfirmModal from '@/components/common/ConfirmModal.vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    },
    categories: {
        type: [Array, Object],
        default: () => []
    }
});

const emit = defineEmits(['auth-error']);

const toast = useToast();

const { updateItem, removeItem, categoryChildren } =
    useCaseStudy('caseStudies');

const forms = ref([]);
const editing = ref([]);
const saving = ref(false);
const deleting = ref(false);
const adding = ref(false);

const currentPage = ref(1);
const perPage = 6;

const showDeleteModal = ref(false);
const deleteTarget = ref(null);

const categoryOptions = computed(() => {
    const categories = categoryChildren.value ?? [];

    return categories
        .map((category) => ({
            id: category?.id ?? category?.name,
            name: category?.name ?? ''
        }))
        .filter((category) => category.id && category.name);
});

const createForm = (item = {}) => {
    let itemCategories = item.categories ?? [];

    if (typeof itemCategories === 'string') {
        try {
            itemCategories = JSON.parse(itemCategories);
        } catch {
            itemCategories = itemCategories
                .split(',')
                .map((category) => category.trim())
                .filter(Boolean);
        }
    }

    if (!Array.isArray(itemCategories)) {
        itemCategories = [];
    }

    itemCategories = itemCategories
        .map((category) => {
            if (typeof category === 'object' && category !== null) {
                return category.name ?? '';
            }

            return String(category).trim();
        })
        .filter(Boolean);

    return {
        id: item.id ?? null,
        title: item.title ?? '',
        description: item.description ?? '',
        categories: [...itemCategories],
        categoryInput: '',
        active: item.active ?? true,
        imageFile: null,
        imagePreview: item.image?.url ?? item.image ?? '',
        isNew: item.id == null
    };
};

const totalPages = computed(() => {
    return Math.max(1, Math.ceil(forms.value.length / perPage));
});

const paginatedForms = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    const end = start + perPage;

    return forms.value.slice(start, end).map((form, index) => ({
        form,
        index: start + index
    }));
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

watch(
    () => props.items,
    (items) => {
        const oldForms = forms.value;
        const oldEditing = editing.value;

        const nextForms = Array.isArray(items)
            ? items.map((item) => {
                  const oldIndex = oldForms.findIndex(
                      (form) => String(form.id) === String(item.id)
                  );

                  const existingForm =
                      oldIndex >= 0 ? oldForms[oldIndex] : null;

                  if (!existingForm) {
                      return createForm(item);
                  }

                  return {
                      ...existingForm,
                      ...createForm(item),

                      categoryInput: existingForm.categoryInput,

                      imageFile: existingForm.imageFile,

                      imagePreview: existingForm.imageFile
                          ? existingForm.imagePreview
                          : (item.image?.url ?? item.image ?? '')
                  };
              })
            : [];

        forms.value = nextForms;

        editing.value = nextForms.map((form) => {
            const oldIndex = oldForms.findIndex(
                (oldForm) => String(oldForm.id) === String(form.id)
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

    if (!file.type || !file.type.startsWith('image/')) {
        toast.error('Please select an image file.');
        event.target.value = '';
        return;
    }

    if (forms.value[index].imagePreview?.startsWith('blob:')) {
        URL.revokeObjectURL(forms.value[index].imagePreview);
    }

    forms.value[index].imageFile = file;
    forms.value[index].imagePreview = URL.createObjectURL(file);

    event.target.value = '';
};

const addCaseStudy = () => {
    if (saving.value || deleting.value || adding.value) {
        return;
    }

    const editingIndex = editing.value.findIndex(Boolean);

    if (editingIndex !== -1) {
        cancel(editingIndex);
    }

    const newForm = createForm({
        title: '',
        description: '',
        categories: [],
        active: true
    });

    forms.value.unshift(newForm);
    editing.value.unshift(true);

    currentPage.value = 1;
    adding.value = true;

    requestAnimationFrame(() => {
        adding.value = false;
    });
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
        const original = props.items.find(
            (item) => String(item.id) === String(form.id)
        );

        if (original) {
            forms.value[index] = createForm(original);
        }
    }

    editing.value[index] = false;
};

const addCategory = (index) => {
    const form = forms.value[index];

    if (!form) {
        return;
    }

    const value = form.categoryInput?.trim();

    if (!value) {
        return;
    }

    const exists = form.categories.some(
        (category) => String(category).toLowerCase() === value.toLowerCase()
    );

    if (exists) {
        toast.warning('This category already exists.');

        form.categoryInput = '';

        return;
    }

    if (form.categories.length >= 2) {
        toast.warning('Maximum 2 categories allowed.');

        form.categoryInput = '';

        return;
    }

    form.categories.push(value);
    form.categoryInput = '';
};

const removeCategory = (index, categoryIndex) => {
    const form = forms.value[index];

    if (!form) {
        return;
    }

    form.categories.splice(categoryIndex, 1);
};

const saveCaseStudyItem = async (index) => {
    const form = forms.value[index];

    if (!form) {
        return;
    }

    if (!form.title?.trim()) {
        toast.error('Please enter a title.');
        return;
    }

    if (!form.description?.trim()) {
        toast.error('Please enter description.');
        return;
    }

    if (!form.categories.length) {
        toast.error('Please add at least one category.');
        return;
    }

    if (form.categories.length > 2) {
        toast.error('Maximum 2 categories allowed.');
        return;
    }

    const isNew = !form.id;

    const payload = {
        ...(form.id
            ? {
                  id: String(form.id)
              }
            : {}),

        title: form.title.trim(),

        description: form.description.trim(),

        categories: form.categories.map((category) => String(category).trim()),

        active: Boolean(form.active)
    };

    saving.value = true;

    try {
        const result = await updateItem(payload, form.imageFile);

        if (form.imagePreview?.startsWith('blob:')) {
            URL.revokeObjectURL(form.imagePreview);
        }

        form.imageFile = null;

        if (isNew && result?.id) {
            form.id = String(result.id);
            form.isNew = false;
        }

        editing.value[index] = false;
    } catch (error) {
        const message =
            error?.graphQLErrors?.[0]?.message ||
            error?.message ||
            'Failed to save case study.';

        if (!error?.graphQLErrors?.length) {
            toast.error(message);
        }

        if (
            error?.graphQLErrors?.some((item) => {
                const errorMessage = String(item.message).toLowerCase();

                return (
                    errorMessage.includes('unauthorized') ||
                    errorMessage.includes('unauthenticated')
                );
            })
        ) {
            emit('auth-error');
        }
    } finally {
        saving.value = false;
    }
};

const openDeleteModal = (item) => {
    if (!item || item.isNew || !item.id) {
        return;
    }

    if (saving.value || deleting.value) {
        return;
    }

    deleteTarget.value = item;
    showDeleteModal.value = true;
};

const cancelDelete = () => {
    if (deleting.value) {
        return;
    }

    showDeleteModal.value = false;
    deleteTarget.value = null;
};

const confirmDelete = async () => {
    const item = deleteTarget.value;

    if (!item?.id) {
        cancelDelete();
        return;
    }

    const itemId = String(item.id);

    showDeleteModal.value = false;
    deleteTarget.value = null;

    deleting.value = true;

    try {
        await removeItem(itemId);

        if (currentPage.value > totalPages.value) {
            currentPage.value = totalPages.value;
        }
    } catch (error) {
        const message =
            error?.graphQLErrors?.[0]?.message ||
            error?.message ||
            'Failed to delete case study.';

        if (!error?.graphQLErrors?.length) {
            toast.error(message);
        }

        if (
            error?.graphQLErrors?.some((item) => {
                const errorMessage = String(item.message).toLowerCase();

                return (
                    errorMessage.includes('unauthorized') ||
                    errorMessage.includes('unauthenticated')
                );
            })
        ) {
            emit('auth-error');
        }
    } finally {
        deleting.value = false;
    }
};
</script>

<template>
    <div>
        <div class="mb-4 mt-7 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">
                    Case Studies
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Manage your case studies.
                </p>
            </div>

            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-[#3674d9] px-3 py-2 text-xs font-medium text-white transition hover:bg-[#2863c5] disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="saving || deleting || adding"
                @click="addCaseStudy"
            >
                <Plus :size="15" />

                Add Case Study
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
                @submit.prevent="saveCaseStudyItem(item.index)"
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
                                class="h-full w-full object-contain p-2"
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
                                        placeholder="Enter case study title"
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
                                        rows="3"
                                        maxlength="300"
                                        placeholder="Enter description"
                                        class="mt-2 w-full resize-none rounded-md border border-slate-200 px-2 py-1 text-xs leading-4 text-slate-500 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                    ></textarea>

                                    <p
                                        v-else
                                        class="mt-2 line-clamp-3 text-xs leading-4 text-slate-500"
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
                                        @click="openDeleteModal(item.form)"
                                    >
                                        <Trash2 :size="16" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 px-4 py-3">
                    <label
                        class="mb-2 block text-[10px] font-medium uppercase tracking-wide text-slate-400"
                    >
                        Categories
                    </label>

                    <div
                        v-if="item.form.categories.length"
                        class="mb-3 flex flex-wrap gap-2"
                    >
                        <span
                            v-for="(category, categoryIndex) in item.form
                                .categories"
                            :key="`${category}-${categoryIndex}`"
                            class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700"
                        >
                            {{ category }}

                            <button
                                v-if="editing[item.index]"
                                type="button"
                                class="flex h-4 w-4 items-center justify-center rounded-full hover:bg-blue-100"
                                @click="
                                    removeCategory(item.index, categoryIndex)
                                "
                            >
                                <X :size="11" />
                            </button>
                        </span>
                    </div>

                    <div v-if="editing[item.index]" class="mt-3">
                        <p
                            v-if="item.form.categories.length >= 2"
                            class="text-xs text-slate-400"
                        >
                            Maximum 2 categories.
                        </p>

                        <div v-else class="flex gap-2">
                            <select
                                v-model="item.form.categoryInput"
                                class="min-w-0 flex-1 rounded-md border border-slate-200 bg-white px-2 py-1.5 text-xs text-slate-700 outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                            >
                                <option value="">Select category</option>

                                <option
                                    v-for="category in categoryOptions"
                                    :key="category.id"
                                    :value="category.name"
                                    :disabled="
                                        item.form.categories.includes(
                                            category.name
                                        )
                                    "
                                >
                                    {{ category.name }}
                                </option>
                            </select>

                            <button
                                type="button"
                                class="rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-600 transition hover:bg-blue-100 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="
                                    !item.form.categoryInput ||
                                    item.form.categories.includes(
                                        item.form.categoryInput
                                    )
                                "
                                @click="addCategory(item.index)"
                            >
                                Add
                            </button>
                        </div>

                        <p
                            v-if="
                                !categoryOptions.length &&
                                item.form.categories.length < 2
                            "
                            class="mt-2 text-xs text-slate-400"
                        >
                            No categories available.
                        </p>
                    </div>

                    <p
                        v-if="
                            !item.form.categories.length && !editing[item.index]
                        "
                        class="text-xs text-slate-400"
                    >
                        No categories
                    </p>
                </div>

                <div
                    v-if="editing[item.index]"
                    class="border-t border-slate-100 bg-slate-50/50 px-4 py-3"
                >
                    <div class="flex items-center justify-end gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="saving"
                            @click="cancel(item.index)"
                        >
                            <X :size="14" />
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-1 rounded-lg bg-[#3674d9] px-3 py-1.5 text-xs font-medium text-white transition hover:bg-[#2863c5] disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="saving"
                        >
                            <Save :size="14" />
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div
            v-else
            class="rounded-xl border border-dashed border-slate-300 py-10 text-center text-sm text-slate-500"
        >
            <p>No case studies found.</p>
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
                    <span class="text-sm"> ‹ </span>
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
                    <span class="text-sm"> › </span>
                </button>
            </div>
        </div>

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Case Study"
            :message="
                deleteTarget?.title
                    ? `Are you sure you want to delete &quot;${deleteTarget.title}&quot;?`
                    : 'Are you sure you want to delete this case study?'
            "
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </div>
</template>
