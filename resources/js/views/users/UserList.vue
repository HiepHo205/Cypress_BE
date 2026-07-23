<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { useMutation, useQuery } from '@vue/apollo-composable';

import BaseTable from '@/components/BaseTable.vue';

import { GET_USERS } from '@/graphql/queries/users';
import {
    DEACTIVATE_USER,
    DELETE_USER
} from '@/graphql/mutations/users';

import { getGraphQLErrorMessages } from '@/utils/graphqlErrors';

const columns = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'role', label: 'Role' },
    { key: 'status', label: 'Status' },
    { key: 'created_at', label: 'Created At' },
    { key: 'actions', label: 'Actions' }
];

const page = ref(1);
const searchName = ref('');
const debouncedSearchName = ref('');

const actionLoading = ref(null);
const actionError = ref(null);

let searchTimer;

const {
    result,
    loading,
    error,
    refetch
} = useQuery(GET_USERS, () => ({
    page: page.value,
    name: debouncedSearchName.value
        ? `%${debouncedSearchName.value}%`
        : undefined
}));

const users = computed(
    () => result.value?.users?.data ?? []
);

const paginatorInfo = computed(
    () => result.value?.users?.paginatorInfo ?? null
);

const errorMessages = computed(() =>
    getGraphQLErrorMessages(
        actionError.value || error.value
    )
);

watch(searchName, () => {
    page.value = 1;

    clearTimeout(searchTimer);

    searchTimer = setTimeout(() => {
        debouncedSearchName.value =
            searchName.value.trim();
    }, 300);
});

onBeforeUnmount(() => {
    clearTimeout(searchTimer);
});

function goToPage(nextPage) {
    page.value = nextPage;
}

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

const { mutate: runDelete } =
    useMutation(DELETE_USER);

const { mutate: runDeactivate } =
    useMutation(DEACTIVATE_USER);

async function deleteUser(id) {
    if (
        !confirm(
            'Are you sure you want to delete this user?'
        )
    ) {
        return;
    }

    actionError.value = null;
    actionLoading.value = id;

    try {
        await runDelete({ id });
        await refetch();
    } catch (e) {
        actionError.value = e;
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
    } catch (e) {
        actionError.value = e;
    } finally {
        actionLoading.value = null;
    }
}
</script>

<template>
    <div>
        <div
            class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between"
        >
            <h1 class="text-2xl font-bold">
                User Management
            </h1>

            <router-link
                to="/admin/users/create"
                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
                Add User
            </router-link>
        </div>

        <div class="mb-4">
            <input
                v-model="searchName"
                type="search"
                placeholder="Search by name..."
                class="w-full max-w-md border rounded-lg px-3 py-2"
            />
        </div>

        <div
            v-if="loading"
            class="text-center py-10"
        >
            Loading...
        </div>

        <div
            v-if="errorMessages.length"
            class="mb-4 bg-red-100 border border-red-300 text-red-700 p-4 rounded"
        >
            <p
                v-for="message in errorMessages"
                :key="message"
            >
                {{ message }}
            </p>
        </div>

        <BaseTable
            v-if="users.length"
            :columns="columns"
            :items="users"
        >
            <template #row="{ item }">
                <td class="px-6 py-4">
                    {{ item.id }}
                </td>

                <td class="px-6 py-4">
                    {{ item.name }}
                </td>

                <td class="px-6 py-4" :title="item.email">
                    {{
                        item.email.length > 22
                            ? item.email.slice(0, 22) + '...'
                            : item.email
                    }}
                </td>
                <td class="px-6 py-4">
                    {{ item.role?.name ?? '-' }}
                </td>

                <td class="px-6 py-4">
                    <span
                        class="px-2 py-1 rounded-full text-xs"
                        :class="
                            item.status === 'active'
                                ? 'bg-green-100 text-green-800'
                                : 'bg-gray-100 text-gray-800'
                        "
                    >
                        {{ item.status }}
                    </span>
                </td>

                <td class="px-6 py-4">
                    {{ formatDate(item.created_at) }}
                </td>

                <td class="px-6 py-4 text-right">
                    <div
                        class="flex justify-end gap-3"
                    >
                        <router-link
                            :to="`/admin/users/${item.id}/edit`"
                            class="text-blue-600"
                        >
                            Edit
                        </router-link>

                        <button
                            v-if="
                                item.status === 'active'
                            "
                            class="text-yellow-600"
                            @click="
                                deactivateUser(item.id)
                            "
                        >
                            Deactivate
                        </button>

                        <button
                            class="text-red-600"
                            @click="
                                deleteUser(item.id)
                            "
                        >
                            Delete
                        </button>
                    </div>
                </td>
            </template>

            <template #mobile="{ item }">
                <div
                    class="bg-white rounded-lg shadow border p-4"
                >
                    <div class="space-y-2">
                        <p>
                            <strong>ID:</strong>
                            {{ item.id }}
                        </p>

                        <p>
                            <strong>Name:</strong>
                            {{ item.name }}
                        </p>

                        <p>
                            <strong>Email:</strong>
                            {{ item.email }}
                        </p>

                        <p>
                            <strong>Status:</strong>

                            <span
                                class="ml-2 px-2 py-1 rounded-full text-xs"
                                :class="
                                    item.status ===
                                    'active'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-gray-100 text-gray-800'
                                "
                            >
                                {{ item.status }}
                            </span>
                        </p>

                        <p>
                            <strong>Created:</strong>
                            {{ formatDate(
                                item.created_at
                            ) }}
                        </p>
                    </div>

                    <div
                        class="flex flex-wrap gap-4 mt-4 pt-3 border-t"
                    >
                        <router-link
                            :to="`/admin/users/${item.id}/edit`"
                            class="text-blue-600"
                        >
                            Edit
                        </router-link>

                        <button
                            v-if="
                                item.status === 'active'
                            "
                            class="text-yellow-600"
                            @click="
                                deactivateUser(item.id)
                            "
                        >
                            Deactivate
                        </button>

                        <button
                            class="text-red-600"
                            @click="
                                deleteUser(item.id)
                            "
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </template>
        </BaseTable>

        <div
            v-else-if="!loading"
            class="text-center py-10 bg-white rounded-lg shadow"
        >
            No users found.
        </div>

        <div
            v-if="paginatorInfo"
            class="flex justify-between items-center mt-4"
        >
            <button
                class="px-3 py-2 border rounded"
                :disabled="
                    page <= 1 || loading
                "
                @click="goToPage(page - 1)"
            >
                Previous
            </button>

            <span>
                Page
                {{ paginatorInfo.currentPage }}
                /
                {{ paginatorInfo.lastPage }}
            </span>

            <button
                class="px-3 py-2 border rounded"
                :disabled="
                    page >=
                        paginatorInfo.lastPage ||
                    loading
                "
                @click="goToPage(page + 1)"
            >
                Next
            </button>
        </div>
    </div>
</template>