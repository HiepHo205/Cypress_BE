<template>
    <div class="flex justify-between items-center p-4 border-t border-gray-200">

        <button @click="$emit('prev')" :disabled="currentPage === 1"
            class="px-4 py-2 border rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
            Previous
        </button>
        <span class="text-sm text-gray-500">
            Page {{ currentPage }} / {{ totalPages }}
        </span>
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


defineEmits([
    "prev",
    "next"
]);
</script>