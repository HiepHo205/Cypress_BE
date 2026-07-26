<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false
    },
    message: {
        type: String,
        default: 'Loading...'
    },
    fullScreen: {
        type: Boolean,
        default: true
    }
});
</script>
<template>
    <Teleport v-if="fullScreen" to="body">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="flex flex-col items-center rounded-2xl bg-white px-8 py-6 shadow-xl">
                <div class="h-12 w-12 animate-spin rounded-full border-4 border-gray-200 border-t-blue-600"></div>

                <p class="mt-4 text-sm font-medium text-gray-700">
                    {{ message }}
                </p>
            </div>
        </div>
    </Teleport>

    <div v-else-if="show"
        class="absolute inset-0 z-10 flex items-center justify-center rounded-2xl bg-white/70 backdrop-blur-sm">
        <div class="flex flex-col items-center rounded-2xl bg-white px-8 py-6 shadow-xl">
            <div class="h-12 w-12 animate-spin rounded-full border-4 border-gray-200 border-t-blue-600"></div>

            <p class="mt-4 text-sm font-medium text-gray-700">
                {{ message }}
            </p>
        </div>
    </div>
</template>