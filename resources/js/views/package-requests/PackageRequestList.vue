<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useQuery } from '@vue/apollo-composable';

import BaseTable from '@/components/BaseTable.vue';

import { GET_PACKAGE_REQUESTS } from '@/graphql/queries/package-requests';

const router = useRouter();

const { result, loading } = useQuery(GET_PACKAGE_REQUESTS);

const items = computed(() => result.value?.packageRequests ?? []);

const columns = [
    {
        key: 'id',
        label: 'ID'
    },
    {
        key: 'plan',
        label: 'Plan'
    },
    {
        key: 'full_name',
        label: 'Name'
    },
    {
        key: 'email',
        label: 'Email'
    },
    {
        key: 'status',
        label: 'Status'
    },
    {
        key: 'action',
        label: 'Action'
    }
];

function getStatusClass(status) {
    switch (status) {
        case 'approved':
            return 'bg-green-100 text-green-700';

        case 'rejected':
            return 'bg-red-100 text-red-700';

        default:
            return 'bg-yellow-100 text-yellow-700';
    }
}
</script>

<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold">Package Requests</h1>
        </div>

        <div v-if="loading">Loading...</div>

        <BaseTable v-else :columns="columns" :items="items">
            <template #row="{ item }">
                <td class="px-6 py-4">
                    {{ item.id }}
                </td>

                <td class="px-6 py-4">
                    {{ item.plan_name || item.plan_id || '-' }}
                </td>

                <td class="px-6 py-4">
                    {{ item.full_name }}
                </td>

                <td class="px-6 py-4">
                    {{
                        item.email?.length > 23
                            ? item.email.slice(0, 23) + '...'
                            : item.email
                    }}
                </td>

                <td class="px-6 py-4">
                    <span
                        class="px-3 py-1 rounded-full text-sm"
                        :class="getStatusClass(item.status)"
                    >
                        {{ item.status }}
                    </span>
                </td>

                <td class="px-6 py-4">
                    <button
                        @click="
                            router.push(`/admin/package-requests/${item.id}`)
                        "
                        class="text-blue-600 hover:underline"
                    >
                        View
                    </button>
                </td>
            </template>
        </BaseTable>
    </div>
</template>
