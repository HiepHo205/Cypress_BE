<script setup lang="ts">
import { computed, watch, ref } from 'vue';
import { ImageIcon, Plus, Save, Trash2 } from 'lucide-vue-next';
import { useQuery } from '@vue/apollo-composable';
import { GET_ROLES } from '../../../graphql/queries/role';
import { useHomepage } from '../../../composables/home/useHomepage';
const { loading, getSection, saveSection, removeItem } = useHomepage();
const { result: rolesResult } = useQuery(GET_ROLES);
const roles = computed(() => rolesResult.value?.roles ?? []);
const successStories = ref<any[]>([]);
const selectedId = ref<string | number | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const successStoriesData = getSection('successStories');
const selectedItem = computed(() => {
    return (
        successStories.value.find((item) => item.id === selectedId.value) ??
        null
    );
});
const caseStudiesData = getSection('caseStudies');
const categories = computed(() => {
    const studies = caseStudiesData.value?.caseStudies ?? [];

    return [
        ...new Set(
            studies.flatMap((item: any) =>
                (item.seriesTags ?? '')
                    .split(',')
                    .map((tag: string) => tag.trim())
                    .filter(Boolean)
            )
        )
    ];
});
const toggleCategory = (category: string) => {
    if (!selectedItem.value) return;

    if (!Array.isArray(selectedItem.value.categories)) {
        selectedItem.value.categories = [];
    }

    const index = selectedItem.value.categories.indexOf(category);

    if (index > -1) {
        selectedItem.value.categories.splice(index, 1);
    } else {
        selectedItem.value.categories.push(category);
    }
};
const loadSuccessStories = () => {
    const data = successStoriesData.value;

    if (!data) return;
    successStories.value = (data.successStories ?? []).map((item: any) => ({
        id: item.id,
        name: item.name ?? '',
        role: item.role ?? '',
        company: item.company ?? '',
        quote: item.quote ?? '',
        categories: item.categories
            ? String(item.categories)
                  .split(',')
                  .map((category) => category.trim())
                  .filter(Boolean)
            : [],
        avatar: null,
        avatarData: item.avatar ?? null,
        avatarPreview: item.avatar?.url ?? ''
    }));

    selectedId.value = successStories.value[0]?.id ?? null;
};
const addItem = () => {
    const item = {
        id: `temp-${Date.now()}`,
        name: '',
        role: '',
        company: '',
        quote: '',
        categories: [],
        avatar: null,
        avatarPreview: ''
    };
    successStories.value.push(item);
    selectedId.value = item.id;
};

const removeSelected = async () => {
    if (!selectedItem.value) return;
    const item = selectedItem.value;
    if (String(item.id).startsWith('temp-')) {
        successStories.value = successStories.value.filter(
            (i) => i.id !== item.id
        );
    } else {
        await removeItem('success_stories', 'successStories', item.id);
        await loadSuccessStories();
    }

    selectedId.value = successStories.value[0]?.id ?? null;
};

const saveChanges = async () => {
    if (!selectedItem.value) return;

    const item = selectedItem.value;

    const isCreate = String(item.id).startsWith('temp-');
    const payloadItem = {
        id: isCreate ? null : item.id,
        name: item.name,
        role: item.role,
        company: item.company,
        quote: item.quote,
        categories: Array.isArray(item.categories)
            ? item.categories.join(',')
            : item.categories,
    };

    if (!item.avatar) {
        payloadItem.avatar = item.avatarData;
    }

   await saveSection(
    'success_stories',
    {
        successStories: [
            {
                ...payloadItem
            }
        ]
    },
    item.avatar,
    null,
    isCreate ? 'create' : 'update'
);

    await loadSuccessStories();
};
const openFilePicker = () => {
    fileInput.value?.click();
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file || !selectedItem.value) return;
    selectedItem.value.avatar = file;
    selectedItem.value.avatarPreview = URL.createObjectURL(file);
};

watch(
    successStoriesData,
    (value) => {
        if (value) {
            loadSuccessStories();
        }
    },
    {
        immediate: true
    }
);
</script>

<template>
    <section class="space-y-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                Success Stories Management
            </h2>

            <p class="text-sm text-gray-500">
                Manage homepage success stories.
            </p>
        </div>

        <div class="grid gap-6 xl:grid-cols-12">
            <div class="xl:col-span-4">
                <div class="rounded-xl border bg-white p-4 shadow-sm">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-semibold">Stories</h3>

                            <p class="text-sm text-gray-500">Select story</p>
                        </div>

                        <button
                            @click="addItem"
                            class="rounded-lg bg-blue-600 p-2 text-white"
                        >
                            <Plus :size="18" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-2">
                        <button
                            v-for="item in successStories"
                            :key="item.id"
                            @click="selectedId = item.id"
                            class="w-full rounded-lg border p-3 text-left"
                            :class="
                                selectedId === item.id
                                    ? 'border-blue-600 bg-blue-50'
                                    : 'border-gray-200'
                            "
                        >
                            <p class="font-medium">
                                {{ item.name || 'Untitled' }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ item.company }}
                            </p>
                        </button>
                    </div>
                </div>
            </div>

         
<div class="xl:col-span-8">
    <template v-if="selectedItem">
        <div class="mb-5">
            <label class="block text-lg font-semibold text-gray-900">
                {{
                    String(selectedItem.id).startsWith('temp-')
                        ? 'Create Success Story'
                        : 'Edit Success Story'
                }}
            </label>

            <p class="mt-1 text-sm text-gray-500">
                {{
                    String(selectedItem.id).startsWith('temp-')
                        ? 'Create a new homepage success story.'
                        : 'Edit homepage success story information.'
                }}
            </p>
        </div>

        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="text-sm">
                        Name
                    </label>

                    <input
                        v-model="selectedItem.name"
                        class="w-full rounded-lg border px-3 py-2"
                    />
                </div>

                <div>
                    <label
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Role
                    </label>

                    <select
                        v-model="selectedItem.role"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                    >
                        <option value="">
                            Select role
                        </option>

                        <option
                            v-for="role in roles"
                            :key="role.id"
                            :value="role.role_name"
                        >
                            {{ role.role_name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm">
                        Company
                    </label>

                    <input
                        v-model="selectedItem.company"
                        class="w-full rounded-lg border px-3 py-2"
                    />
                </div>

                <div>
                    <label class="text-sm">
                        Quote
                    </label>

                    <textarea
                        v-model="selectedItem.quote"
                        rows="3"
                        class="w-full rounded-lg border px-3 py-2"
                    />
                </div>

                <div class="col-span-2">
                    <label class="mb-2 block text-sm font-medium">
                        Categories
                    </label>

                    <div
                        class="max-h-60 overflow-y-auto rounded-lg border border-gray-200 p-3"
                    >
                        <div class="grid grid-cols-2 gap-3">
                            <button
                                v-for="category in categories"
                                :key="category"
                                type="button"
                                @click="toggleCategory(category)"
                                class="flex items-center gap-3 rounded-xl border p-3 text-left transition"
                                :class="
                                    selectedItem.categories.includes(
                                        category
                                    )
                                        ? 'border-blue-600 bg-blue-50'
                                        : 'border-gray-200'
                                "
                            >
                                <input
                                    type="checkbox"
                                    :checked="
                                        selectedItem.categories.includes(
                                            category
                                        )
                                    "
                                />

                                <span>{{ category }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <input
                        ref="fileInput"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleFileChange"
                    />

                    <button
                        type="button"
                        @click="openFilePicker"
                        class="flex h-32 w-32 items-center justify-center rounded-xl border-2 border-dashed"
                    >
                        <img
                            v-if="selectedItem.avatarPreview"
                            :src="selectedItem.avatarPreview"
                            class="h-full w-full object-contain"
                        />

                        <ImageIcon v-else />
                    </button>
                </div>
            </div>

            <div class="mt-5 flex justify-end gap-3">
                <button
                    type="button"
                    @click="removeSelected"
                    class="rounded-lg border border-red-300 px-4 py-2 text-red-600"
                >
                    <Trash2 :size="18" />
                </button>

                <button
                    type="button"
                    @click="saveChanges"
                    :disabled="loading"
                    class="flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-white"
                >
                    <Save :size="18" />
                </button>
            </div>
        </div>
    </template>

    <div
        v-else
        class="rounded-xl border border-dashed bg-white p-10 text-center text-gray-500"
    >
        Select a success story or create a new one.
    </div>
</div>

        </div>
    </section>
</template>
