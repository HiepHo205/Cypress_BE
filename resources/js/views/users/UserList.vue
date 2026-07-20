<template>
    <div>
        <div
            class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between"
        >
            <h1 class="text-2xl font-bold">User Management</h1>
            <router-link
                to="/admin/users/create"
                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700"
            >
                Add User
            </router-link>
        </div>

        <div class="mb-4">
            <input
                v-model="searchName"
                type="search"
                placeholder="Search by name..."
                class="w-full max-w-md border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>

        <div v-if="loading" class="text-center py-8 text-gray-500">
            Loading...
        </div>

        <div
            v-if="errorMessages.length"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
            role="alert"
        >
            <strong class="font-bold">Error!</strong>
            <p
                v-for="message in errorMessages"
                :key="message"
                class="block sm:inline"
            >
                {{ message }}
            </p>
        </div>

        <div
            v-if="users.length"
            class="overflow-x-auto bg-white rounded-lg shadow"
        >
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            ID
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Name
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Email
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Status
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Created At
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="user in users" :key="user.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ user.id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ user.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ user.email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                :class="
                                    user.status === 'active'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-gray-100 text-gray-800'
                                "
                            >
                                {{ user.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ new Date(user.created_at).toLocaleDateString() }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-right space-x-2"
                        >
                            <router-link
                                :to="`/admin/users/${user.id}/edit`"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                            >
                                Edit
                            </router-link>
                            <button
                                v-if="user.status === 'active'"
                                type="button"
                                class="text-yellow-600 hover:text-yellow-800 text-sm font-medium disabled:opacity-50"
                                :disabled="actionLoading === user.id"
                                @click="deactivateUser(user.id)"
                            >
                                Deactivate
                            </button>
                            <button
                                type="button"
                                class="text-red-600 hover:text-red-800 text-sm font-medium disabled:opacity-50"
                                :disabled="actionLoading === user.id"
                                @click="deleteUser(user.id)"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div
                v-if="paginatorInfo"
                class="flex items-center justify-between px-6 py-4 border-t bg-gray-50"
            >
                <span class="text-sm text-gray-600">
                    Page {{ paginatorInfo.currentPage }} of
                    {{ paginatorInfo.lastPage }}
                </span>
                <div class="space-x-2">
                    <button
                        type="button"
                        class="px-3 py-1 text-sm border rounded disabled:opacity-50"
                        :disabled="page <= 1 || loading"
                        @click="goToPage(page - 1)"
                    >
                        Previous
                    </button>
                    <button
                        type="button"
                        class="px-3 py-1 text-sm border rounded disabled:opacity-50"
                        :disabled="page >= paginatorInfo.lastPage || loading"
                        @click="goToPage(page + 1)"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>

        <div
            v-else-if="!loading && !errorMessages.length"
            class="text-center py-8 text-gray-500 bg-white rounded-lg shadow"
        >
            No users found.
        </div>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { useMutation, useQuery } from '@vue/apollo-composable';
import { GET_USERS } from '@/graphql/queries/users';
import { DEACTIVATE_USER, DELETE_USER } from '@/graphql/mutations/users';
import { getGraphQLErrorMessages } from '@/utils/graphqlErrors';

const page = ref(1);
const searchName = ref('');
const debouncedSearchName = ref('');
const actionLoading = ref(null);
const actionError = ref(null);

let searchTimer;

const { result, loading, error, refetch } = useQuery(GET_USERS, () => ({
    page: page.value,
    name: debouncedSearchName.value
        ? `%${debouncedSearchName.value}%`
        : undefined
}));

const users = computed(() => result.value?.users?.data ?? []);
const paginatorInfo = computed(
    () => result.value?.users?.paginatorInfo ?? null
);
const errorMessages = computed(() =>
    getGraphQLErrorMessages(actionError.value || error.value)
);

watch(searchName, () => {
    page.value = 1;

    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        debouncedSearchName.value = searchName.value.trim();
    }, 300);
});

onBeforeUnmount(() => clearTimeout(searchTimer));

function goToPage(nextPage) {
    page.value = nextPage;
}

const { mutate: runDelete } = useMutation(DELETE_USER);
const { mutate: runDeactivate } = useMutation(DEACTIVATE_USER);

async function deleteUser(id) {
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }

    actionError.value = null;
    actionLoading.value = id;
    try {
        await runDelete({ id });
        await refetch();
    } catch (mutationError) {
        actionError.value = mutationError;
    } finally {
        actionLoading.value = null;
    }
}

async function deactivateUser(id) {
    actionError.value = null;
    actionLoading.value = id;
    try {
        await runDeactivate({ id });
        await refetch();
    } catch (mutationError) {
        actionError.value = mutationError;
    } finally {
        actionLoading.value = null;
    }
}
</script>
