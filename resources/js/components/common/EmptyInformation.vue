<template>
    <div class="w-full border-r border-gray-200 bg-white p-2">
        <div
            class="flex min-h-[285px] flex-col items-center justify-center rounded-2xl border border-dashed border-gray-800 bg-white"
        >
            <div class="flex flex-col items-center justify-center">
                <div class="mb-4 flex h-10 w-10 items-center justify-center">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="h-10 w-10 text-gray-400"
                    >
                        <rect
                            x="3"
                            y="3"
                            width="18"
                            height="18"
                            rx="2"
                            ry="2"
                        />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="M21 15l-5-5L5 21" />
                    </svg>
                </div>

                <p class="mb-6 text-xl font-semibold text-gray-600">
                    No information found
                </p>

                <button
                    type="button"
                    @click="$emit('add')"
                    class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-xl font-medium text-white transition hover:bg-blue-700"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="h-6 w-6"
                    >
                        <path d="M12 5v14M5 12h14" />
                    </svg>

                    <span>Add Information</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineEmits(['add'])
</script>