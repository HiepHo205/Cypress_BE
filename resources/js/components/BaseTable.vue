<script setup>
defineProps({
    columns: {
        type: Array,
        required: true
    },
    items: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <div class="overflow-x-auto rounded-lg bg-white shadow">
        <table class="min-w-[900px] divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        {{ column.label }}
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                <tr
                    v-for="item in items"
                    :key="item.id"
                    class="hover:bg-gray-50"
                >
                    <slot
                        name="row"
                        :item="item"
                    />
                </tr>
            </tbody>
        </table>
    </div>
</template>