<script setup>
import { Plus, Trash2, GripVertical, X, Save } from 'lucide-vue-next';

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
    'add-sub',
    'remove-sub',
    'save'
]);

const updateTitle = (index, value) => {
    const items = [...props.modelValue];

    if (!items[index]) {
        return;
    }

    items[index] = {
        ...items[index],
        title: value
    };

    emit('update:modelValue', items);
};

const updateChild = (parentIndex, childIndex, value) => {
    const items = [...props.modelValue];

    if (!items[parentIndex]) {
        return;
    }

    const children = [...(items[parentIndex].children ?? [])];

    children[childIndex] = value;

    items[parentIndex] = {
        ...items[parentIndex],
        children
    };

    emit('update:modelValue', items);
};

const handleSave = () => {
    const items = Array.isArray(props.modelValue) ? props.modelValue : [];

    emit('save', items);
};
</script>

<template>
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-[14px] font-semibold text-gray-800">
                    Table of Content
                </h2>

                <p class="mt-1 text-[11px] text-gray-400">
                    Main items use 1, 2, 3 and sub items use 1.1, 1.2...
                </p>
            </div>

            <button
                type="button"
                :disabled="saving"
                class="flex h-8 items-center gap-1.5 rounded-lg border border-gray-200 px-3 text-[11px] font-medium text-gray-600 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                @click="emit('add')"
            >
                <Plus :size="14" />
                Add section
            </button>
        </div>

        <div class="space-y-3">
            <div
                v-for="(item, index) in modelValue"
                :key="item.id ?? index"
                class="rounded-lg border border-gray-200 p-3"
            >
                <div class="flex items-start gap-2">
                    <GripVertical
                        :size="15"
                        class="mt-2 shrink-0 text-gray-300"
                    />

                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span
                                class="w-6 shrink-0 text-xs font-semibold text-[#2874d0]"
                            >
                                {{ index + 1 }}.
                            </span>

                            <input
                                :value="item.title ?? ''"
                                type="text"
                                placeholder="Main section"
                                :disabled="saving"
                                class="h-9 flex-1 rounded-md border border-gray-200 px-3 text-[11px] text-gray-700 outline-none focus:border-[#2874d0] disabled:bg-gray-50"
                                @input="updateTitle(index, $event.target.value)"
                            />
                        </div>

                        <div
                            v-if="item.children?.length"
                            class="mt-3 space-y-2 pl-6"
                        >
                            <div
                                v-for="(child, childIndex) in item.children"
                                :key="childIndex"
                                class="flex items-center gap-2"
                            >
                                <span
                                    class="w-8 shrink-0 text-[10px] font-medium text-gray-400"
                                >
                                    {{ index + 1 }}.{{ childIndex + 1 }}
                                </span>

                                <input
                                    :value="child ?? ''"
                                    type="text"
                                    placeholder="Sub content"
                                    :disabled="saving"
                                    class="h-8 flex-1 rounded-md border border-gray-200 px-2 text-[10px] text-gray-600 outline-none focus:border-[#2874d0] disabled:bg-gray-50"
                                    @input="
                                        updateChild(
                                            index,
                                            childIndex,
                                            $event.target.value
                                        )
                                    "
                                />

                                <button
                                    type="button"
                                    :disabled="saving"
                                    class="text-gray-400 transition hover:text-red-500 disabled:opacity-50"
                                    @click="
                                        emit('remove-sub', index, childIndex)
                                    "
                                >
                                    <X :size="13" />
                                </button>
                            </div>
                        </div>

                        <button
                            type="button"
                            :disabled="saving"
                            class="mt-2 ml-6 text-[10px] font-medium text-[#2874d0] disabled:opacity-50"
                            @click="emit('add-sub', index)"
                        >
                            + Add sub content
                        </button>
                    </div>

                    <button
                        type="button"
                        :disabled="saving"
                        class="mt-1 text-gray-400 transition hover:text-red-500 disabled:opacity-50"
                        @click="emit('remove', index)"
                    >
                        <Trash2 :size="15" />
                    </button>
                </div>
            </div>

            <div
                v-if="!modelValue.length"
                class="rounded-lg border border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-center text-[11px] text-gray-400"
            >
                No table of content sections.
            </div>
        </div>

        <div class="mt-6 flex justify-end border-t border-gray-100 pt-4">
            <button
                type="button"
                :disabled="saving"
                class="flex h-10 items-center gap-2 rounded-lg bg-[#2874d0] px-5 text-[12px] font-medium text-white transition hover:bg-[#1f63b5] disabled:cursor-not-allowed disabled:opacity-60"
                @click="handleSave"
            >
                <Save :size="15" :class="{ 'animate-pulse': saving }" />
            </button>
        </div>
    </section>
</template>
