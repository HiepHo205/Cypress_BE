<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

defineProps<{
    currentPage: number;
    totalPages: number;
}>();

const emit = defineEmits<{
    change: [page: number];
}>();

const changePage = (page: number) => {
    emit('change', page);
};
</script>

<template>
    <div
        v-if="totalPages > 1"
        class="mt-6 flex items-center justify-center gap-2"
    >
        <button
            type="button"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="currentPage === 1"
            @click="changePage(currentPage - 1)"
        >
            <ChevronLeft :size="16" />
        </button>

        <button
            v-for="page in totalPages"
            :key="page"
            type="button"
            class="h-8 min-w-8 rounded-lg px-2 text-xs font-medium transition"
            :class="
                page === currentPage
                    ? 'bg-[#3674d9] text-white'
                    : 'border border-slate-200 text-slate-600 hover:bg-slate-50'
            "
            @click="changePage(page)"
        >
            {{ page }}
        </button>

        <button
            type="button"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="currentPage === totalPages"
            @click="changePage(currentPage + 1)"
        >
            <ChevronRight :size="16" />
        </button>
    </div>
</template>
