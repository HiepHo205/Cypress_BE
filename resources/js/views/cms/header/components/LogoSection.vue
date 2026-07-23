<template>
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Website Logo</h2>
        <p class="mt-1 text-sm text-gray-500">
            Configure the primary logo displayed in the website header.
        </p>

        <div class="mt-6 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-10">
            <div class="flex flex-col items-center">
                <img src="https://placehold.co/240x80?text=Cypress+Logo" alt="Website Logo"
                    class="h-20 object-contain" />

                <p class="mt-6 text-base font-semibold text-gray-700">
                    Cypress Logo
                </p>

                <p class="mt-2 text-sm text-gray-500">
                    PNG • 240 × 80 px
                </p>
            </div>
        </div>

        <div class="mt-6 flex justify-between">
            <button
                class="rounded-xl bg-red-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-red-600">
                Remove Logo
            </button>

            <button
                class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700">
                Save Changes
            </button>
        </div>
    </div>
</template>

<script setup>
</script>