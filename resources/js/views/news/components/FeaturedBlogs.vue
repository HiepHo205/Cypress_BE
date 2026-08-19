<script setup lang="ts">
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import { Upload, Save, X, Edit3, Trash2, Plus } from 'lucide-vue-next';
import { useNews } from '../../../composables/news/useNews';
import { useToast } from 'vue-toastification';
import type { NewsForm, NewsItem } from '../../../types/news';
import { usePagination } from '../../../composables/common/usePagination';
import Pagination from '../../../components/common/Pagination.vue';
import ConfirmModal from '../../../components/common/ConfirmModal.vue';

const props = defineProps<{
    items: NewsItem[];
}>();

const router = useRouter();

const { updateItem, removeItem, news } = useNews('featured');
const toast = useToast();

const { paginatedItems, currentPage, totalPages, goToPage } = usePagination(
    () => (Array.isArray(props.items) ? props.items : []),
    6
);

const forms = ref<NewsForm[]>([]);
const editing = ref<boolean[]>([]);
const saving = ref(false);
const deleting = ref(false);
const adding = ref(false);

const showDeleteModal = ref(false);
const deleteTarget = ref<NewsForm | null>(null);
const deleteTargetIndex = ref<number | null>(null);

const categories = computed(() => {
    const value = news.value?.postCategories;

    if (!Array.isArray(value)) {
        return [];
    }

    return value;
});

const categoryOptions = computed<string[]>(() => {
    const options = categories.value
        .map((category: any) => {
            if (typeof category === 'string') {
                return category;
            }

            return (
                category?.title ?? category?.name ?? category?.category ?? ''
            );
        })
        .filter(Boolean);

    return [...new Set(options)];
});

const createForm = (item: Partial<NewsItem> = {}): NewsForm => {
    return {
        id: item.id ?? null,
        title: item.title ?? '',
        description: item.description ?? '',
        date: item.date ?? '',
        category: item.category ?? '',
        imageFile: null,
        imagePreview:
            typeof item.image === 'string'
                ? item.image
                : (item.image?.url ?? ''),
        featured: item.featured ?? true,
        isNew: item.id == null
    };
};

watch(
    paginatedItems,
    (items) => {
        const safeItems = Array.isArray(items) ? items : [];

        const oldForms = forms.value;
        const oldEditing = editing.value;

        const nextForms = safeItems.map((item) => {
            const oldIndex = oldForms.findIndex((form) => form.id === item.id);

            const oldForm = oldIndex >= 0 ? oldForms[oldIndex] : undefined;

            if (!oldForm) {
                return createForm(item);
            }

            return {
                ...createForm(item),
                ...oldForm,
                id: item.id,
                title: oldForm.title,
                description: oldForm.description,
                date: oldForm.date,
                category: oldForm.category,
                featured: oldForm.featured,
                imageFile: oldForm.imageFile,
                imagePreview: oldForm.imageFile
                    ? oldForm.imagePreview
                    : typeof item.image === 'string'
                      ? item.image
                      : (item.image?.url ?? ''),
                isNew: false
            };
        });

        const nextEditing = safeItems.map((item) => {
            const oldIndex = oldForms.findIndex((form) => form.id === item.id);

            return oldIndex >= 0 ? Boolean(oldEditing[oldIndex]) : false;
        });

        forms.value = nextForms;
        editing.value = nextEditing;
    },
    {
        immediate: true
    }
);

const handlePageChange = (page: number): void => {
    if (saving.value || deleting.value || adding.value) {
        return;
    }

    const pageNumber = Number(page);

    if (!Number.isFinite(pageNumber)) {
        return;
    }

    const safePage = Math.max(1, Math.min(pageNumber, totalPages.value || 1));

    const editingIndex = editing.value.findIndex(Boolean);

    if (editingIndex !== -1) {
        cancel(editingIndex);
    }

    goToPage(safePage);
};

const goToDetail = async (id: string | null): Promise<void> => {
    if (!id) {
        return;
    }

    if (saving.value || deleting.value || adding.value) {
        return;
    }

    if (editing.value.some(Boolean)) {
        return;
    }

    await router.push({
        name: 'news.detail',
        params: {
            id
        },
        query: {
            tab: 'news'
        }
    });
};

const handleImageUpload = (event: Event, index: number): void => {
    const target = event.target as HTMLInputElement;

    const file = target.files?.[0];

    if (!file) {
        return;
    }

    const form = forms.value[index];

    if (!form) {
        return;
    }

    if (form.imagePreview && form.imagePreview.startsWith('blob:')) {
        URL.revokeObjectURL(form.imagePreview);
    }

    form.imageFile = file;
    form.imagePreview = URL.createObjectURL(file);
};

const addBlogItem = (): void => {
    if (saving.value || deleting.value || adding.value) {
        return;
    }

    const currentIndex = editing.value.findIndex(Boolean);

    if (currentIndex !== -1) {
        cancel(currentIndex);
    }

    const defaultCategory =
        categoryOptions.value.length > 0 ? categoryOptions.value[0] : '';

    const newForm = createForm({
        id: null,
        title: '',
        description: '',
        date: new Date().toISOString().slice(0, 10),
        category: defaultCategory,
        featured: true
    });

    forms.value.unshift(newForm);
    editing.value.unshift(true);

    adding.value = true;

    requestAnimationFrame(() => {
        adding.value = false;
    });
};

const startEdit = (index: number): void => {
    if (saving.value || deleting.value || adding.value) {
        return;
    }

    const form = forms.value[index];

    if (!form) {
        return;
    }

    const currentIndex = editing.value.findIndex(Boolean);

    if (currentIndex !== -1 && currentIndex !== index) {
        cancel(currentIndex);
    }

    editing.value[index] = true;
};

const cancel = (index: number): void => {
    const form = forms.value[index];

    if (!form) {
        return;
    }

    if (form.isNew) {
        if (form.imagePreview && form.imagePreview.startsWith('blob:')) {
            URL.revokeObjectURL(form.imagePreview);
        }

        forms.value.splice(index, 1);
        editing.value.splice(index, 1);

        return;
    }

    const original = props.items.find((item) => item.id === form.id);

    if (original) {
        forms.value[index] = createForm(original);
    }

    editing.value[index] = false;
};

const saveBlog = async (index: number): Promise<void> => {
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
        featured: true
    };

    saving.value = true;

    try {
        const savedItem = await updateItem(payload, form.imageFile);

        if (form.imagePreview && form.imagePreview.startsWith('blob:')) {
            URL.revokeObjectURL(form.imagePreview);
        }

        forms.value[index] = {
            ...form,
            ...(savedItem ?? {}),
            id: savedItem?.id ?? form.id,
            title: form.title.trim(),
            description: form.description?.trim() || '',
            date: form.date,
            category: form.category,
            featured: true,
            imageFile: null,
            isNew: false
        };

        editing.value[index] = false;
    } catch (error) {
        console.error(error);
        toast.error('Failed to save news item.');
    } finally {
        saving.value = false;
    }
};

const openDeleteModal = (item: NewsForm, index: number): void => {
    if (!item || item.isNew || !item.id) {
        return;
    }

    if (saving.value || deleting.value) {
        return;
    }

    deleteTarget.value = item;
    deleteTargetIndex.value = index;
    showDeleteModal.value = true;
};

const cancelDelete = (): void => {
    if (deleting.value) {
        return;
    }

    showDeleteModal.value = false;
    deleteTarget.value = null;
    deleteTargetIndex.value = null;
};

const confirmDelete = async (): Promise<void> => {
    const item = deleteTarget.value;

    if (!item?.id) {
        cancelDelete();
        return;
    }

    showDeleteModal.value = false;

    const itemId = item.id;

    deleteTarget.value = null;
    deleteTargetIndex.value = null;

    deleting.value = true;

    try {
        await removeItem(itemId);
    } catch (error) {
        console.error(error);
        toast.error('Failed to delete news item.');
    } finally {
        deleting.value = false;
    }
};

onBeforeUnmount(() => {
    forms.value.forEach((form) => {
        if (form.imagePreview && form.imagePreview.startsWith('blob:')) {
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
                    Featured News
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Manage your featured news articles.
                </p>
            </div>

            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-[#3674d9] px-3 py-2 text-xs font-medium text-white transition hover:bg-[#2863c5] disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="saving || deleting || adding"
                @click="addBlogItem"
            >
                <Plus :size="15" />
                Add Featured News
            </button>
        </div>

        <div v-if="forms.length" class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <form
                v-for="(form, index) in forms"
                :key="form.id ?? `new-${index}`"
                class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white"
                @submit.prevent="saveBlog(index)"
            >
                <div
                    class="p-4"
                    :class="!editing[index] ? 'cursor-pointer' : ''"
                    @click="!editing[index] && goToDetail(form.id)"
                >
                    <div class="flex gap-4">
                        <label
                            class="group relative flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-100"
                            :class="
                                editing[index]
                                    ? 'cursor-pointer'
                                    : 'cursor-pointer'
                            "
                            @click="
                                editing[index]
                                    ? $event.stopPropagation()
                                    : goToDetail(form.id)
                            "
                        >
                            <img
                                v-if="form.imagePreview"
                                :src="form.imagePreview"
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
                                v-if="editing[index]"
                                class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition group-hover:opacity-100"
                            >
                                <Upload :size="20" class="text-white" />
                            </div>

                            <input
                                v-if="editing[index]"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleImageUpload($event, index)"
                            />
                        </label>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <input
                                        v-if="editing[index]"
                                        v-model="form.title"
                                        type="text"
                                        required
                                        placeholder="Enter news title"
                                        class="w-full rounded-md border border-slate-200 px-2 py-1 text-sm font-semibold text-slate-800 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                        @click.stop
                                    />

                                    <h3
                                        v-else
                                        class="truncate text-sm font-semibold text-slate-800"
                                    >
                                        {{ form.title || 'Untitled' }}
                                    </h3>

                                    <textarea
                                        v-if="editing[index]"
                                        v-model="form.description"
                                        rows="2"
                                        maxlength="200"
                                        placeholder="Enter description"
                                        class="mt-2 w-full resize-none rounded-md border border-slate-200 px-2 py-1 text-xs leading-4 text-slate-500 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                        @click.stop
                                    ></textarea>

                                    <p
                                        v-else
                                        class="mt-2 line-clamp-2 text-xs leading-4 text-slate-500"
                                    >
                                        {{
                                            form.description || 'No description'
                                        }}
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-1">
                                    <button
                                        v-if="!form.isNew"
                                        type="button"
                                        class="rounded-md p-1.5 text-blue-600 transition hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        :disabled="
                                            editing[index] || saving || deleting
                                        "
                                        @click.stop="startEdit(index)"
                                    >
                                        <Edit3 :size="16" />
                                    </button>

                                    <button
                                        v-if="!form.isNew"
                                        type="button"
                                        class="rounded-md p-1.5 text-red-500 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        :disabled="
                                            editing[index] || saving || deleting
                                        "
                                        @click.stop="
                                            openDeleteModal(form, index)
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
                    <div
                        class="px-4 py-3"
                        @click="!editing[index] && goToDetail(form.id)"
                    >
                        <label
                            class="mb-1 block text-[10px] font-medium uppercase tracking-wide text-slate-400"
                        >
                            Category
                        </label>

                        <select
                            v-if="editing[index]"
                            v-model="form.category"
                            class="w-full rounded-md border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                            :class="
                                form.category
                                    ? 'text-slate-600'
                                    : 'text-slate-400'
                            "
                            @click.stop
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
                            {{ form.category || 'No category' }}
                        </p>

                        <p
                            v-if="
                                editing[index] && categoryOptions.length === 0
                            "
                            class="mt-1 text-[10px] text-red-500"
                        >
                            No categories found
                        </p>
                    </div>

                    <div
                        class="border-l border-slate-100 px-4 py-3"
                        @click="!editing[index] && goToDetail(form.id)"
                    >
                        <label
                            class="mb-1 block text-[10px] font-medium uppercase tracking-wide text-slate-400"
                        >
                            Date
                        </label>

                        <input
                            v-if="editing[index]"
                            v-model="form.date"
                            type="date"
                            class="w-full rounded-md border border-slate-200 px-2 py-1 text-xs text-slate-600 outline-none focus:border-blue-400"
                            @click.stop
                        />

                        <p v-else class="text-xs text-slate-600">
                            {{ form.date }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="editing[index]"
                    class="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50/50 px-4 py-3"
                >
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="saving"
                        @click="cancel(index)"
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
            </form>
        </div>

        <div
            v-else
            class="rounded-xl border border-dashed border-slate-300 py-10 text-center text-sm text-slate-500"
        >
            <p>No featured news found.</p>
        </div>

        <Pagination
            v-if="totalPages > 1"
            :current-page="currentPage"
            :total-pages="totalPages"
            @change="handlePageChange"
        />

        <ConfirmModal
            :show="showDeleteModal"
            title="Delete Featured News"
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
