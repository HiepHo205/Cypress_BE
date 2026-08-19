<script setup>
import { computed } from 'vue';
import { useQuery } from '@vue/apollo-composable';

import BaseTable from '@/components/BaseTable.vue';

import {
    GET_LOCATIONS
} from '@/graphql/queries/locations';

const { result, loading } =
    useQuery(GET_LOCATIONS);

const locations = computed(
    () => result.value?.locations ?? []
);

const columns = [
    {
        key: 'id',
        label: 'ID'
    },
    {
        key: 'name',
        label: 'Location'
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
        case 'active':
            return 'bg-green-100 text-green-700';

        case 'inactive':
            return 'bg-red-100 text-red-700';

        default:
            return 'bg-gray-100 text-gray-700';
    }
}
</script>

<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">
                Locations
            </h1>

            <router-link
                to="/admin/locations/create"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg"
            >
                Add Location
            </router-link>
        </div>

        <div
            v-if="loading"
            class="text-center py-10"
        >
            Loading...
        </div>

        <BaseTable
            v-else
            :columns="columns"
            :items="locations"
        >
            <template #row="{ item }">
                <td class="px-4 py-4 w-14 whitespace-nowrap">
                    {{ item.id }}
                </td>

                <td class="px-6 py-4">
                    {{ item.name }}
                </td>

                <td class="px-6 py-4">
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-md text-xs"
                        :class="getStatusClass(item.status)"
                    >
                        {{ item.status }}
                    </span>
                </td>

                <td class="px-6 py-4">
                    <router-link
                        :to="`/admin/locations/${item.id}`"
                        class="text-blue-600 hover:underline"
                    >
                        Detail
                    </router-link>
                </td>
            </template>
        </BaseTable>
    </div>
</template>