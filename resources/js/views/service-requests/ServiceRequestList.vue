<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useQuery } from '@vue/apollo-composable';

import BaseTable from '@/components/BaseTable.vue';

import {
    GET_SERVICE_REQUESTS
} from '@/graphql/queries/service-requests';

const router = useRouter();

const {
    result,
    loading
} = useQuery(
    GET_SERVICE_REQUESTS
);

const columns = [
    {
        key: 'id',
        label: 'ID'
    },
    {
        key: 'request_type',
        label: 'Type'
    },
    {
        key: 'full_name',
        label: 'Full Name'
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

const items = computed(
    () =>
        result.value
            ?.serviceRequests ?? []
);

function viewDetail(id) {
    router.push(
        `/admin/service-requests/${id}`
    );
}
</script>

<template>
    <div class="space-y-6">

        <div>
            <h1
                class="text-2xl font-bold text-gray-900"
            >
                Service Requests
            </h1>

            <p
                class="text-sm text-gray-500 mt-1"
            >
                Manage client requests submitted from the website.
            </p>
        </div>

        <div
            v-if="loading"
            class="bg-white rounded-lg shadow p-6"
        >
            Loading...
        </div>

        <BaseTable
            v-else
            :columns="columns"
            :items="items"
        >
            <template #row="{ item }">

                <td class="px-6 py-4">
                    #{{ item.id }}
                </td>

                <td class="px-6 py-4">
                    <span
                        class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700"
                    >
                        {{ item.request_type }}
                    </span>
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
                        class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700"
                    >
                        {{ item.status }}
                    </span>

                </td>

                <td class="px-6 py-4">

                    <button
                        class="text-indigo-600 hover:text-indigo-800 font-medium"
                        @click="
                            viewDetail(
                                item.id
                            )
                        "
                    >
                        View
                    </button>

                </td>

            </template>
        </BaseTable>

    </div>
</template>