<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: "Confirm Delete"
    },
    message: {
        type: String,
        default: "Are you sure you want to delete this item?"
    }
});

const emit = defineEmits([
    "confirm",
    "cancel"
]);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ title }}
            </h3>

            <p class="mt-3 text-sm text-gray-500">
                {{ message }}
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button @click="emit('cancel')" class="rounded-lg border px-4 py-2 text-gray-600">
                    Cancel
                </button>

                <button @click="emit('confirm')" class="rounded-lg bg-red-500 px-4 py-2 text-white">
                    Delete
                </button>
            </div>
        </div>
    </div>
</template>