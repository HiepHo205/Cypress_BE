<template>
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Countdown & Action Button</h2>

        <p class="mt-1 text-sm text-gray-500">
            Configure the countdown timer and primary action button displayed in the website header.
        </p>

        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Countdown Date
                </label>

                <input type="datetime-local" value="2026-12-31T23:59" readonly
                    class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm outline-none" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Status
                </label>

                <select class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm outline-none"
                    disabled>
                    <option selected>Enabled</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Button Text
                </label>

                <input type="text" value="Book Now" readonly
                    class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm outline-none" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Button URL
                </label>

                <input type="text" value="https://cypresshub.com/register" readonly
                    class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm outline-none" />
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-blue-100 bg-blue-50 p-6">
            <h3 class="text-sm font-semibold text-blue-700">
                Preview
            </h3>

            <div
                class="mt-5 flex flex-col gap-5 rounded-xl bg-white p-6 shadow-sm md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">
                        Event Starts In
                    </p>

                    <div class="mt-2 flex gap-3">
                        <div class="rounded-lg bg-slate-900 px-4 py-3 text-center text-white">
                            <div class="text-xl font-bold">120</div>
                            <div class="text-xs">Days</div>
                        </div>

                        <div class="rounded-lg bg-slate-900 px-4 py-3 text-center text-white">
                            <div class="text-xl font-bold">08</div>
                            <div class="text-xs">Hours</div>
                        </div>

                        <div class="rounded-lg bg-slate-900 px-4 py-3 text-center text-white">
                            <div class="text-xl font-bold">35</div>
                            <div class="text-xs">Minutes</div>
                        </div>

                        <div class="rounded-lg bg-slate-900 px-4 py-3 text-center text-white">
                            <div class="text-xl font-bold">42</div>
                            <div class="text-xs">Seconds</div>
                        </div>
                    </div>
                </div>

                <button class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                    Book Now
                </button>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button
                class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                Save Changes
            </button>
        </div>
    </div>
</template>

<script setup>
</script>