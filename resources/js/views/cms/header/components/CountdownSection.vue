<script setup>
import { reactive, computed, onMounted, onUnmounted, ref } from "vue";
import useHeaderCountdown from "@/composables/header/useHeaderCountdown";

const emit = defineEmits(["loading"]);

const { getCountdown, updateCountdown } = useHeaderCountdown();

const form = reactive({
    target_date: "",
    enabled: true,
    button_label: "",
    button_href: ""
});

const currentTime = ref(new Date());
let timer = null;

onMounted(async () => {
    timer = setInterval(() => {
        currentTime.value = new Date();
    }, 1000);

    emit("loading", true);

    try {
        const data = await getCountdown();

        if (!data) return;

        form.target_date = data.target_date
            ? data.target_date.slice(0, 16)
            : "";

        form.enabled = data.enabled;
        form.button_label = data.button?.label ?? "";
        form.button_href = data.button?.href ?? "";
    } finally {
        emit("loading", false);
    }
});

onUnmounted(() => {
    clearInterval(timer);
});
const countdown = computed(() => {
    if (!form.target_date) {
        return {
            days: 0,
            hours: "00",
            minutes: "00",
            seconds: "00",
        };
    }

    const selectedDate = new Date(form.target_date);

    const targetDate = new Date(
        selectedDate.getFullYear(),
        11,
        31,
        23,
        59,
        59
    );

    const now = currentTime.value;
    const startTime = now < selectedDate ? selectedDate : now;

    let diff = targetDate.getTime() - startTime.getTime();

    if (diff <= 0) {
        return {
            days: 0,
            hours: "00",
            minutes: "00",
            seconds: "00",
        };
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    diff %= 1000 * 60 * 60 * 24;

    const hours = Math.floor(diff / (1000 * 60 * 60));
    diff %= 1000 * 60 * 60;

    const minutes = Math.floor(diff / (1000 * 60));
    diff %= 1000 * 60;

    const seconds = Math.floor(diff / 1000);

    return {
        days,
        hours: String(hours).padStart(2, "0"),
        minutes: String(minutes).padStart(2, "0"),
        seconds: String(seconds).padStart(2, "0"),
    };
});
const save = async () => {
    emit("loading", true);

    const payload = {
        enabled: form.enabled,
        target_date: form.target_date
            ? form.target_date.replace("T", " ") + ":00"
            : null,
        cta: {
            label: form.button_label,
            href: form.button_href
        }
    };

    try {
        await updateCountdown(payload);
    } finally {
        emit("loading", false);
    }
};
</script>
<template>
    <div class="relative rounded-2xl">
        <div :class="loading ? 'pointer-events-none opacity-50' : ''">
            <h2 class="text-lg font-semibold text-gray-900">
                Countdown & Action Button
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Configure the countdown timer and primary action button displayed in the website header.
            </p>

            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Countdown Date
                    </label>

                    <input v-model="form.target_date" type="datetime-local"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-blue-500" />
                </div>


                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Button Text
                    </label>

                    <input v-model="form.button_label" type="text"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-blue-500" />
                </div>


                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Button URL
                    </label>

                    <input v-model="form.button_href" type="text"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-blue-500" />
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
                            <div class="rounded-lg bg-blue-600 px-4 py-3 text-center text-white">
                                <div class="text-xl font-bold">
                                    {{ countdown.days }}
                                </div>

                                <div class="text-xs">
                                    Days
                                </div>
                            </div>

                            <div class="rounded-lg bg-blue-600 px-4 py-3 text-center text-white">
                                <div class="text-xl font-bold">
                                    {{ countdown.hours }}
                                </div>

                                <div class="text-xs">
                                    Hours
                                </div>
                            </div>

                            <div class="rounded-lg bg-blue-600 px-4 py-3 text-center text-white">
                                <div class="text-xl font-bold">
                                    {{ countdown.minutes }}
                                </div>

                                <div class="text-xs">
                                    Minutes
                                </div>
                            </div>

                            <div class="rounded-lg bg-blue-600 px-4 py-3 text-center text-white">
                                <div class="text-xl font-bold">
                                    {{ countdown.seconds }}
                                </div>

                                <div class="text-xs">
                                    Seconds
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white">
                        {{ form.button_label || 'Book Now' }}
                    </button>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                    @click="save">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</template>