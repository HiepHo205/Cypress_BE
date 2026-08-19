<script setup>
import {
    Plus,
    Trash2,
    ChevronUp,
    ChevronDown,
    Upload,
    X,
    Save
} from 'lucide-vue-next';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    saving: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits([
    'update:modelValue',
    'add',
    'remove',
    'move-up',
    'move-down',
    'upload-image',
    'remove-image',
    'save'
]);

const updateSection = (index, field, value) => {
    const sections = [...props.modelValue];

    sections[index] = {
        ...sections[index],
        [field]: value
    };

    emit('update:modelValue', sections);
};

const getImageUrl = (image) => {
    if (!image) {
        return '';
    }

    if (typeof image === 'string') {
        return image;
    }

    if (typeof image === 'object') {
        return image.url ?? '';
    }

    return '';
};
</script>

<template>
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-[14px] font-semibold text-gray-800">Content</h2>

                <p class="mt-1 text-[11px] text-gray-400">
                    Add detailed content below the table of contents.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    :disabled="saving"
                    class="flex h-8 items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 text-[11px] font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="emit('save')"
                >
                    <Save :size="14" />
                    {{ saving ? 'Saving...' : 'Save' }}
                </button>

                <button
                    type="button"
                    :disabled="saving"
                    class="flex h-8 items-center gap-1.5 rounded-lg bg-[#2874d0] px-3 text-[11px] font-medium text-white hover:bg-[#2167bd] disabled:cursor-not-allowed disabled:opacity-50"
                    @click="emit('add')"
                >
                    <Plus :size="14" />
                    Add content
                </button>
            </div>
        </div>

        <div
            v-if="modelValue.length"
            class="max-h-[720px] overflow-y-auto pr-2"
        >
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <article
                    v-for="(section, index) in modelValue"
                    :key="section.id ?? index"
                    class="rounded-xl border border-gray-200 bg-gray-50 p-4"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-7 w-7 items-center justify-center rounded-md bg-white text-[10px] font-semibold text-gray-500"
                            >
                                {{ index + 1 }}
                            </div>

                            <span
                                class="text-[12px] font-semibold text-gray-700"
                            >
                                Content {{ index + 1 }}
                            </span>
                        </div>

                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                :disabled="index === 0 || saving"
                                class="flex h-7 w-7 items-center justify-center rounded-md bg-white text-gray-400 hover:text-gray-700 disabled:opacity-40"
                                @click="emit('move-up', index)"
                            >
                                <ChevronUp :size="14" />
                            </button>

                            <button
                                type="button"
                                :disabled="
                                    index === modelValue.length - 1 || saving
                                "
                                class="flex h-7 w-7 items-center justify-center rounded-md bg-white text-gray-400 hover:text-gray-700 disabled:opacity-40"
                                @click="emit('move-down', index)"
                            >
                                <ChevronDown :size="14" />
                            </button>

                            <button
                                type="button"
                                :disabled="saving"
                                class="ml-1 flex h-7 w-7 items-center justify-center rounded-md bg-white text-gray-400 hover:text-red-500 disabled:opacity-40"
                                @click="emit('remove', index)"
                            >
                                <Trash2 :size="14" />
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="mb-3">
                            <label
                                class="mb-1.5 block text-[10px] font-medium text-gray-500"
                            >
                                Heading
                            </label>

                            <input
                                :value="section.title ?? ''"
                                type="text"
                                :disabled="saving"
                                placeholder="Enter heading..."
                                class="h-10 w-full rounded-md border border-gray-200 bg-white px-3 text-[11px] text-gray-700 outline-none focus:border-[#2874d0] disabled:bg-gray-100"
                                @input="
                                    updateSection(
                                        index,
                                        'title',
                                        $event.target.value
                                    )
                                "
                            />
                        </div>

                        <label
                            class="mb-1.5 block text-[10px] font-medium text-gray-500"
                        >
                            Description
                        </label>

                        <textarea
                            :value="section.content ?? ''"
                            rows="7"
                            placeholder="Enter description..."
                            :disabled="saving"
                            class="w-full resize-y rounded-md border border-gray-200 bg-white px-3 py-2.5 text-[11px] leading-5 text-gray-600 outline-none focus:border-[#2874d0] disabled:bg-gray-100"
                            @input="
                                updateSection(
                                    index,
                                    'content',
                                    $event.target.value
                                )
                            "
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-[10px] font-medium text-gray-500"
                        >
                            Image
                        </label>

                        <div
                            v-if="getImageUrl(section.image)"
                            class="relative overflow-hidden rounded-lg border border-gray-200 bg-white"
                        >
                            <img
                                :src="getImageUrl(section.image)"
                                alt=""
                                class="h-[180px] w-full object-cover"
                            />

                            <button
                                type="button"
                                :disabled="saving"
                                class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-white text-gray-500 shadow hover:text-red-500 disabled:opacity-40"
                                @click="emit('remove-image', section)"
                            >
                                <X :size="14" />
                            </button>
                        </div>

                        <label
                            v-else
                            class="flex h-[180px] cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white transition hover:border-[#2874d0]"
                        >
                            <Upload :size="20" class="mb-2 text-gray-400" />

                            <span class="text-[11px] font-medium text-gray-500">
                                Upload image
                            </span>

                            <span class="mt-1 text-[9px] text-gray-400">
                                PNG, JPG, WEBP
                            </span>

                            <input
                                type="file"
                                accept="image/png,image/jpeg,image/webp"
                                class="hidden"
                                :disabled="saving"
                                @change="emit('upload-image', $event, section)"
                            />
                        </label>
                    </div>
                </article>
            </div>
        </div>

        <div
            v-else
            class="rounded-lg border border-dashed border-gray-200 bg-gray-50 py-8 text-center"
        >
            <p class="text-xs text-gray-400">No content added.</p>

            <button
                type="button"
                :disabled="saving"
                class="mt-2 text-xs font-medium text-[#2874d0] disabled:opacity-50"
                @click="emit('add')"
            >
                + Add content
            </button>
        </div>
    </section>
</template>
