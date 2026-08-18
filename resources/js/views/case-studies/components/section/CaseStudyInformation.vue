<script setup>
import { computed, reactive, watch } from 'vue';
import { Upload, Save } from 'lucide-vue-next';
import { useCaseStudy } from '@/composables/caseStudy/useCaseStudy';

const props = defineProps({
    caseStudy: {
        type: Object,
        required: true
    },
    seriesOptions: {
        type: Array,
        default: () => []
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

const emit = defineEmits(['update:caseStudy', 'upload-logo', 'save']);

const form = reactive({
    id: '',
    title: '',
    date: '',
    author: '',
    logo: '',
    seriesTags: [],
    categories: ['', ''],
    description: ''
});

watch(
    () => props.caseStudy,
    (value) => {
        if (!value) return;

        form.id = value.id ? String(value.id) : '';
        form.title = value.title ?? '';
        form.date = value.date ?? '';
        form.author = value.author ?? '';
        form.logo = value.logo?.url ?? value.logo ?? '';
        form.seriesTags = Array.isArray(value.seriesTags)
            ? [...value.seriesTags]
            : [];
        form.categories = Array.isArray(value.categories)
            ? [value.categories[0] ?? '', value.categories[1] ?? '']
            : ['', ''];
        form.description = value.description ?? '';
    },
    {
        immediate: true,
        deep: true
    }
);

const { loading, updateItem } = useCaseStudy('caseStudies');

const isSaving = computed(() => {
    return props.saving || loading.value;
});

const emitForm = () => {
    emit('update:caseStudy', {
        id: form.id,
        title: form.title,
        date: form.date,
        author: form.author,
        logo: form.logo,
        seriesTags: [...form.seriesTags],
        categories: [...form.categories],
        description: form.description
    });
};

const updateField = (field, value) => {
    form[field] = value;
    emitForm();
};

const updateCategory = (index, value) => {
    const currentCategories = Array.isArray(form.categories)
        ? [...form.categories]
        : ['', ''];

    while (currentCategories.length < 2) {
        currentCategories.push('');
    }

    currentCategories[index] = value;
    form.categories = currentCategories;
    emitForm();
};

const toggleSeries = (series) => {
    const current = Array.isArray(form.seriesTags) ? [...form.seriesTags] : [];

    const index = current.indexOf(series);

    if (index >= 0) {
        current.splice(index, 1);
    } else {
        current.push(series);
    }

    form.seriesTags = current;
    emitForm();
};

const buildPayload = () => {
    return {
        id: String(form.id),
        title: String(form.title ?? '').trim(),
        date: form.date ?? '',
        author: form.author ?? '',
        logo: form.logo || null,
        seriesTags: Array.isArray(form.seriesTags) ? [...form.seriesTags] : [],
        categories: Array.isArray(form.categories)
            ? form.categories.filter(Boolean).slice(0, 2)
            : [],
        description: form.description ?? ''
    };
};

const handleLogoUpload = async (event) => {
    const file = event?.target?.files?.[0];

    if (!file) return;

    if (!form.id || !form.title.trim()) {
        event.target.value = '';
        return;
    }

    try {
        const updated = await updateItem(buildPayload(), null, file);

        if (updated?.logo) {
            form.logo = updated.logo?.url ?? updated.logo ?? '';
        }

        emitForm();
    } finally {
        event.target.value = '';
    }
};

const handleSave = async () => {
    if (!form.id || !form.title.trim() || !form.description.trim()) {
        return;
    }

    const categories = Array.isArray(form.categories)
        ? form.categories.filter(Boolean).slice(0, 2)
        : [];

    if (!categories.length) {
        return;
    }

    try {
        const updated = await updateItem({
            ...buildPayload(),
            categories
        });

        if (updated && typeof updated === 'object') {
            form.id = String(updated.id ?? form.id);
            form.title = updated.title ?? form.title;
            form.date = updated.date ?? form.date;
            form.author = updated.author ?? form.author;
            form.logo = updated.logo?.url ?? updated.logo ?? form.logo;
            form.seriesTags = Array.isArray(updated.seriesTags)
                ? [...updated.seriesTags]
                : form.seriesTags;
            form.categories = Array.isArray(updated.categories)
                ? [updated.categories[0] ?? '', updated.categories[1] ?? '']
                : form.categories;
            form.description = updated.description ?? form.description;
        }

        emitForm();

        emit('save', {
            id: form.id,
            title: form.title,
            date: form.date,
            author: form.author,
            logo: form.logo,
            seriesTags: [...form.seriesTags],
            categories: [...form.categories],
            description: form.description
        });
    } catch {
        return;
    }
};
</script>

<template>
    <div class="min-w-0 xl:col-span-1">
        <section class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="mb-5">
                <h2 class="text-[14px] font-semibold text-gray-800">
                    Case Study Information
                </h2>

                <p class="mt-1 text-[11px] text-gray-400">
                    Manage basic information for this case study.
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
                        placeholder="Enter case study title..."
                        class="h-10 w-full rounded-lg border border-gray-200 px-3 text-[12px] text-gray-700 outline-none focus:border-[#2874d0]"
                        @input="updateField('title', $event.target.value)"
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
                        class="h-10 w-full rounded-lg border border-gray-200 px-3 text-[12px] text-gray-700 outline-none focus:border-[#2874d0]"
                        @input="updateField('date', $event.target.value)"
                    />
                </div>

                <div>
                    <label
                        class="mb-1.5 block text-[11px] font-medium text-gray-600"
                    >
                        Series
                    </label>

                    <div
                        v-if="seriesOptions.length"
                        class="grid grid-cols-2 gap-2"
                    >
                        <label
                            v-for="series in seriesOptions"
                            :key="series"
                            class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2.5 transition"
                            :class="
                                form.seriesTags.includes(series)
                                    ? 'border-[#2874d0] bg-blue-50'
                                    : 'border-gray-200 bg-white hover:border-[#2874d0]'
                            "
                        >
                            <input
                                type="checkbox"
                                :checked="form.seriesTags.includes(series)"
                                class="h-4 w-4 rounded accent-[#2874d0]"
                                @change="toggleSeries(series)"
                            />

                            <span
                                class="text-[11px] font-medium"
                                :class="
                                    form.seriesTags.includes(series)
                                        ? 'text-[#2874d0]'
                                        : 'text-gray-600'
                                "
                            >
                                {{ series }}
                            </span>
                        </label>
                    </div>

                    <p
                        v-else
                        class="rounded-lg border border-dashed border-gray-200 bg-gray-50 px-3 py-3 text-[11px] text-gray-400"
                    >
                        No series available.
                    </p>
                </div>

                <div>
                    <label
                        class="mb-1.5 block text-[11px] font-medium text-gray-600"
                    >
                        Author & Logo
                    </label>

                    <div class="flex items-center gap-3">
                        <input
                            :value="form.author"
                            type="text"
                            placeholder="Author"
                            class="h-10 min-w-0 flex-1 rounded-lg border border-gray-200 px-3 text-[12px] text-gray-700 outline-none focus:border-[#2874d0]"
                            @input="updateField('author', $event.target.value)"
                        />

                        <div class="w-20 shrink-0">
                            <label
                                class="group relative flex h-10 w-20 cursor-pointer items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50 transition hover:border-[#2874d0]"
                                :class="{
                                    'pointer-events-none opacity-60': isSaving
                                }"
                            >
                                <img
                                    v-if="form.logo"
                                    :src="form.logo"
                                    alt="Case study logo"
                                    class="h-full w-full object-contain p-1 transition group-hover:opacity-50"
                                />

                                <Upload
                                    v-else
                                    :size="15"
                                    class="text-gray-400 transition group-hover:text-[#2874d0]"
                                />

                                <div
                                    v-if="form.logo"
                                    class="absolute inset-0 flex items-center justify-center bg-black/0 transition group-hover:bg-black/20"
                                >
                                    <span
                                        class="rounded-md bg-white/90 px-2 py-1 text-[9px] font-medium text-gray-700 opacity-0 shadow transition group-hover:opacity-100"
                                    >
                                        Change
                                    </span>
                                </div>

                                <input
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    :disabled="isSaving"
                                    @change="handleLogoUpload"
                                />
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label
                        class="mb-2 block text-[11px] font-medium text-gray-600"
                    >
                        Categories
                    </label>

                    <div class="space-y-3">
                        <select
                            v-for="index in 2"
                            :key="index"
                            :value="form.categories[index - 1] ?? ''"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-[12px] text-gray-700 outline-none focus:border-[#2874d0]"
                            @change="
                                updateCategory(index - 1, $event.target.value)
                            "
                        >
                            <option value="">Select category</option>

                            <optgroup
                                v-for="group in categories"
                                :key="group.id"
                                :label="group.title"
                            >
                                <option
                                    v-for="child in group.children || []"
                                    :key="child.id"
                                    :value="child.name"
                                >
                                    {{ child.name }}
                                </option>
                            </optgroup>
                        </select>
                    </div>
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
                        placeholder="Enter case study description..."
                        class="w-full resize-y rounded-lg border border-gray-200 px-3 py-2.5 text-[12px] leading-6 text-gray-600 outline-none focus:border-[#2874d0]"
                        @input="updateField('description', $event.target.value)"
                    />
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-gray-100 pt-4">
                <button
                    type="button"
                    :disabled="isSaving"
                    class="flex h-10 items-center gap-2 rounded-lg bg-[#2874d0] px-5 text-[12px] font-medium text-white transition hover:bg-[#1f63b5] disabled:cursor-not-allowed disabled:opacity-60"
                    @click="handleSave"
                >
                    <Save
                        :size="15"
                        :class="{
                            'animate-pulse': isSaving
                        }"
                    />

                    {{ isSaving ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </section>
    </div>
</template>
