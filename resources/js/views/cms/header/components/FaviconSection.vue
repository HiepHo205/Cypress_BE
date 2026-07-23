<template>
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Favicon</h2>

        <p class="mt-1 text-sm text-gray-500">
            Configure the favicon displayed in the browser tab.
        </p>

        <div class="mt-6 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-10">
            <div class="flex flex-col items-center">
                <div
                    class="flex h-20 w-20 items-center justify-center rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <img src="https://placehold.co/64x64?text=F" alt="Favicon" class="h-12 w-12 object-contain" />
                </div>

                <p class="mt-6 text-base font-semibold text-gray-700">
                    favicon.ico
                </p>

                <p class="mt-2 text-sm text-gray-500">
                    ICO • 64 × 64 px
                </p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    File Name
                </label>

                <input type="text" value="favicon.ico" readonly
                    class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-700 outline-none" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Format
                </label>

                <input type="text" value="ICO" readonly
                    class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-700 outline-none" />
            </div>
        </div>

        <div class="mt-8 flex justify-between">
            <button
                class="rounded-xl bg-red-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-red-600">
                Remove Favicon
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