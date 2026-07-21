<template>
    <div v-if="totalPages > 1" class="flex items-center justify-between p-4 border-t">
        <button @click="$emit('prev')" :disabled="currentPage === 1"
            class="px-4 py-2 border rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
            Previous
        </button>

        <div class="flex gap-2">
            <button v-for="page in totalPages" :key="page" @click="$emit('change', page)" :class="[
                'w-9 h-9 rounded-lg border transition',
                currentPage === page
                    ? 'bg-[#2B71D3] border-[#2B71D3] text-white'
                    : 'bg-white hover:bg-gray-100'
            ]">
                {{ page }}
            </button>
        </div>

        <button @click="$emit('next')" :disabled="currentPage === totalPages"
            class="px-4 py-2 border rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
            Next
        </button>
    </div>
</template>

<script setup>
defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    totalPages: {
        type: Number,
        required: true,
    },
});

defineEmits(["prev", "next", "change"]);
</script>