import { ref, computed, watch } from 'vue';

export function useRolePagination(items, perPage = 5) {
    const currentPage = ref(1);

    const totalPages = computed(() => {
        return Math.max(1, Math.ceil(items.value.length / perPage));
    });

    const paginatedItems = computed(() => {
        const start = (currentPage.value - 1) * perPage;
        return items.value.slice(start, start + perPage);
    });

    function nextPage() {
        if (currentPage.value < totalPages.value) {
            currentPage.value++;
        }
    }

    function prevPage() {
        if (currentPage.value > 1) {
            currentPage.value--;
        }
    }

    function goToPage(page) {
        if (page >= 1 && page <= totalPages.value) {
            currentPage.value = page;
        }
    }

    watch(items, () => {
        currentPage.value = 1;
    });

    return {
        currentPage,
        totalPages,
        paginatedItems,
        nextPage,
        prevPage,
        goToPage
    };
}
