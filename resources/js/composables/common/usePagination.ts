import { computed, ref, watch } from 'vue';

export function usePagination<T>(
    items: () => T[] | undefined,
    perPage = 6
) {
    const currentPage = ref(1);

    const totalItems = computed(() => {
        return items()?.length ?? 0;
    });

    const totalPages = computed(() => {
        return Math.max(1, Math.ceil(totalItems.value / perPage));
    });

    const paginatedItems = computed(() => {
        const list = items() ?? [];
        const start = (currentPage.value - 1) * perPage;

        return list.slice(start, start + perPage);
    });

    const goToPage = (page: number) => {
        currentPage.value = Math.min(
            Math.max(page, 1),
            totalPages.value
        );
    };

    const nextPage = () => {
        if (currentPage.value < totalPages.value) {
            currentPage.value++;
        }
    };

    const previousPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
        }
    };

    const resetPage = () => {
        currentPage.value = 1;
    };

    watch(totalItems, () => {
        if (currentPage.value > totalPages.value) {
            currentPage.value = totalPages.value;
        }
    });

    return {
        currentPage,
        totalItems,
        totalPages,
        paginatedItems,
        goToPage,
        nextPage,
        previousPage,
        resetPage
    };
}