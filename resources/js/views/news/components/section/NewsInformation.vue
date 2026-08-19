<script setup>
import { computed, reactive, watch } from 'vue';
import { Upload, Save } from 'lucide-vue-next';
import { useNews } from '@/composables/news/useNews';

const props = defineProps({
    news: {
        type: Object,
        required: true
    },

    categories: {
        type: Array,
        default: () => []
    },

    saving: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits([
    'update:news',
    'upload-image',
    'remove-image',
    'save'
]);

const form = reactive({
    id: '',
    title: '',
    date: '',
    author: '',
    image: '',
    categories: '',
    description: ''
});

const normalizeCategory = (value) => {
    if (!value) {
        return '';
    }

    if (typeof value === 'string') {
        return value.trim();
    }

    if (Array.isArray(value)) {
        if (!value.length) {
            return '';
        }

        return normalizeCategory(value[0]);
    }

    if (typeof value === 'object') {
        return String(
            value.name ??
            value.title ??
            value.category ??
            value.label ??
            ''
        ).trim();
    }

    return String(value).trim();
};

const normalizeCategories = (value) => {
    if (!Array.isArray(value)) {
        return [];
    }

    return value
        .map((item) => {
            if (!item) {
                return null;
            }

            if (typeof item === 'string') {
                const title = item.trim();

                return title
                    ? {
                        id: title,
                        title
                    }
                    : null;
            }

            const title = String(
                item.title ??
                item.name ??
                item.category ??
                item.label ??
                ''
            ).trim();

            if (!title) {
                return null;
            }

            return {
                id: String(item.id ?? title),
                title,
                description: item.description ?? ''
            };
        })
        .filter(Boolean);
};

const normalizeDate = (value) => {
    if (!value) {
        return '';
    }

    if (value instanceof Date) {
        if (Number.isNaN(value.getTime())) {
            return '';
        }

        const year = value.getFullYear();
        const month = String(
            value.getMonth() + 1
        ).padStart(2, '0');
        const day = String(
            value.getDate()
        ).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    if (typeof value !== 'string') {
        return '';
    }

    const date = value.trim();

    if (!date) {
        return '';
    }

    if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
        return date;
    }

    const isoMatch = date.match(
        /^(\d{4})-(\d{2})-(\d{2})T/
    );

    if (isoMatch) {
        return `${isoMatch[1]}-${isoMatch[2]}-${isoMatch[3]}`;
    }

    const parsed = new Date(date);

    if (Number.isNaN(parsed.getTime())) {
        return '';
    }

    const year = parsed.getFullYear();
    const month = String(
        parsed.getMonth() + 1
    ).padStart(2, '0');
    const day = String(
        parsed.getDate()
    ).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const availableCategories = computed(() => {
    return normalizeCategories(props.categories);
});

watch(
    () => props.news,
    (value) => {
        if (!value) {
            return;
        }

        form.id = value.id
            ? String(value.id)
            : '';

        form.title =
            value.title ?? '';

        form.date = normalizeDate(
            value.date
        );

        form.author =
            value.author ?? '';

        form.image =
            value.image?.url ??
            value.image ??
            '';

        form.categories = normalizeCategory(
            value.categories ??
            value.category ??
            ''
        );

        form.description =
            value.description ?? '';
    },
    {
        immediate: true,
        deep: true
    }
);

const { loading, updateItem } =
    useNews('latest');

const isSaving = computed(() => {
    return (
        props.saving ||
        loading.value
    );
});

const emitForm = () => {
    emit('update:news', {
        id: form.id,
        title: form.title,
        date: form.date,
        author: form.author,
        image: form.image,
        categories: form.categories,
        description: form.description
    });
};

const updateField = (
    field,
    value
) => {
    form[field] = value;

    emitForm();
};

const updateCategory = (value) => {
    form.categories =
        normalizeCategory(value);

    emitForm();
};

const buildPayload = () => {
    const category =
        normalizeCategory(
            form.categories
        );

    return {
        id: String(form.id),

        title: String(
            form.title ?? ''
        ).trim(),

        date: normalizeDate(
            form.date
        ),

        author:
            form.author ?? '',

        image:
            form.image || null,

        categories: category
            ? [category]
            : [],

        description:
            form.description ?? ''
    };
};

const handleImageUpload = async (
    event
) => {
    const file =
        event?.target?.files?.[0];

    if (!file) {
        return;
    }

    if (
        !form.id ||
        !form.title.trim()
    ) {
        event.target.value = '';

        return;
    }

    try {
        const updated =
            await updateItem(
                buildPayload(),
                null,
                file
            );

        if (updated?.image) {
            form.image =
                updated.image?.url ??
                updated.image ??
                '';
        }

        if (updated?.date) {
            form.date =
                normalizeDate(
                    updated.date
                );
        }

        emitForm();
    } finally {
        event.target.value = '';
    }
};

const handleSave = async () => {
    if (
        !form.id ||
        !form.title.trim() ||
        !form.description.trim()
    ) {
        return;
    }

    const category =
        normalizeCategory(
            form.categories
        );

    if (!category) {
        return;
    }

    try {
        const updated =
            await updateItem({
                ...buildPayload(),
                categories: [category]
            });

        if (
            updated &&
            typeof updated === 'object'
        ) {
            form.id = String(
                updated.id ??
                form.id
            );

            form.title =
                updated.title ??
                form.title;

            form.date =
                normalizeDate(
                    updated.date ??
                    form.date
                );

            form.author =
                updated.author ??
                form.author;

            form.image =
                updated.image?.url ??
                updated.image ??
                form.image;

            form.categories =
                normalizeCategory(
                    updated.categories ??
                    updated.category ??
                    form.categories
                );

            form.description =
                updated.description ??
                form.description;
        }

        emitForm();

        emit('save', {
            id: form.id,
            title: form.title,
            date: form.date,
            author: form.author,
            image: form.image,
            categories:
                form.categories
                    ? [form.categories]
                    : [],
            description:
                form.description
        });
    } catch {
        return;
    }
};
</script>

<template>
    <div class="min-w-0 xl:col-span-1">
        <section
            class="rounded-xl border border-gray-200 bg-white p-5"
        >
            <div class="mb-5">
                <h2
                    class="text-[14px] font-semibold text-gray-800"
                >
                    News Information
                </h2>

                <p
                    class="mt-1 text-[11px] text-gray-400"
                >
                    Manage basic information
                    for this news article.
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <label
                        class="mb-1.5 block text-[11px] font-medium text-gray-600"
                    >
                        Title
                    </label>

                    <input
                        :value="form.title"
                        type="text"
                        placeholder="Enter news title..."
                        :disabled="isSaving"
                        class="h-10 w-full rounded-lg border border-gray-200 px-3 text-[12px] text-gray-700 outline-none focus:border-[#2874d0] disabled:bg-gray-100"
                        @input="
                            updateField(
                                'title',
                                $event.target.value
                            )
                        "
                    />
                </div>

                <div>
                    <label
                        class="mb-1.5 block text-[11px] font-medium text-gray-600"
                    >
                        Date
                    </label>

                    <input
                        :value="form.date"
                        type="date"
                        :disabled="isSaving"
                        class="h-10 w-full rounded-lg border border-gray-200 px-3 text-[12px] text-gray-700 outline-none focus:border-[#2874d0] disabled:bg-gray-100"
                        @input="
                            updateField(
                                'date',
                                $event.target.value
                            )
                        "
                    />
                </div>

                <div>
                    <label
                        class="mb-1.5 block text-[11px] font-medium text-gray-600"
                    >
                        Author
                    </label>

                    <input
                        :value="form.author"
                        type="text"
                        placeholder="Enter author name..."
                        :disabled="isSaving"
                        class="h-10 w-full rounded-lg border border-gray-200 px-3 text-[12px] text-gray-700 outline-none focus:border-[#2874d0] disabled:bg-gray-100"
                        @input="
                            updateField(
                                'author',
                                $event.target.value
                            )
                        "
                    />
                </div>

                <div>
                    <label
                        class="mb-1.5 block text-[11px] font-medium text-gray-600"
                    >
                        Cover Image
                    </label>

                    <label
                        class="group relative flex h-[150px] cursor-pointer items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50 transition hover:border-[#2874d0]"
                        :class="{
                            'pointer-events-none opacity-60':
                                isSaving
                        }"
                    >
                        <img
                            v-if="form.image"
                            :src="form.image"
                            alt="News cover"
                            class="h-full w-full object-cover transition group-hover:opacity-50"
                        />

                        <div
                            v-else
                            class="flex flex-col items-center justify-center"
                        >
                            <Upload
                                :size="20"
                                class="mb-2 text-gray-400 transition group-hover:text-[#2874d0]"
                            />

                            <span
                                class="text-[11px] font-medium text-gray-500"
                            >
                                Upload cover image
                            </span>

                            <span
                                class="mt-1 text-[9px] text-gray-400"
                            >
                                PNG, JPG, WEBP
                            </span>
                        </div>

                        <div
                            v-if="form.image"
                            class="absolute inset-0 flex items-center justify-center bg-black/0 transition group-hover:bg-black/20"
                        >
                            <span
                                class="rounded-md bg-white/90 px-3 py-1.5 text-[10px] font-medium text-gray-700 opacity-0 shadow transition group-hover:opacity-100"
                            >
                                Change image
                            </span>
                        </div>

                        <input
                            type="file"
                            accept="image/png,image/jpeg,image/webp"
                            class="hidden"
                            :disabled="isSaving"
                            @change="
                                handleImageUpload
                            "
                        />
                    </label>
                </div>

                <div>
                    <label
                        class="mb-2 block text-[11px] font-medium text-gray-600"
                    >
                        Category
                    </label>

                    <select
                        v-if="availableCategories.length"
                        :value="form.categories"
                        :disabled="isSaving"
                        class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-[12px] text-gray-700 outline-none focus:border-[#2874d0] disabled:bg-gray-100"
                        @change="
                            updateCategory(
                                $event.target.value
                            )
                        "
                    >
                        <option value="">
                            Select category
                        </option>

                        <option
                            v-for="category in availableCategories"
                            :key="category.id"
                            :value="category.title"
                        >
                            {{ category.title }}
                        </option>
                    </select>

                    <p
                        v-else
                        class="rounded-lg border border-dashed border-gray-200 bg-gray-50 px-3 py-3 text-[11px] text-gray-400"
                    >
                        No categories available.
                    </p>
                </div>

                <div>
                    <label
                        class="mb-2 block text-[11px] font-medium text-gray-600"
                    >
                        Description
                    </label>

                    <textarea
                        :value="form.description"
                        rows="6"
                        placeholder="Enter news description..."
                        :disabled="isSaving"
                        class="w-full resize-y rounded-lg border border-gray-200 px-3 py-2.5 text-[12px] leading-6 text-gray-600 outline-none focus:border-[#2874d0] disabled:bg-gray-100"
                        @input="
                            updateField(
                                'description',
                                $event.target.value
                            )
                        "
                    />
                </div>
            </div>

            <div
                class="mt-6 flex justify-end border-t border-gray-100 pt-4"
            >
                <button
                    type="button"
                    :disabled="isSaving"
                    class="flex h-10 items-center gap-2 rounded-lg bg-[#2874d0] px-5 text-[12px] font-medium text-white transition hover:bg-[#1f63b5] disabled:cursor-not-allowed disabled:opacity-60"
                    @click="handleSave"
                >
                    <Save
                        :size="15"
                        :class="{
                            'animate-pulse':
                                isSaving
                        }"
                    />

                    {{
                        isSaving
                            ? 'Saving...'
                            : 'Save Changes'
                    }}
                </button>
            </div>
        </section>
    </div>
</template>