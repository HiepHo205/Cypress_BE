<template>
    <div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">
                Subscription Plans
            </h1>
        </div>

        <div
            class="bg-white rounded-lg shadow overflow-hidden"
        >
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left">
                            Plan Name
                        </th>

                        <th class="px-4 py-3 text-left">
                            Price
                        </th>

                        <th class="px-4 py-3 text-right">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <tr v-if="loading">
                        <td
                            colspan="3"
                            class="px-4 py-8 text-center text-gray-500"
                        >
                            Loading plans...
                        </td>
                    </tr>

                    <template v-else>

                        <tr
                            v-for="plan in plans"
                            :key="plan.id"
                            class="border-t"
                        >
                            <td class="px-4 py-3">
                                {{ plan.plan_name }}
                            </td>

                            <td class="px-4 py-3">
                                {{ plan.price }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div
                                    class="flex justify-end gap-4"
                                >
                                    <router-link
                                        :to="`/admin/plans/${plan.id}`"
                                        class="text-indigo-600"
                                    >
                                        Detail
                                    </router-link>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!plans.length">
                            <td
                                colspan="3"
                                class="px-4 py-8 text-center text-gray-500"
                            >
                                No plans found.
                            </td>
                        </tr>

                    </template>

                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useQuery } from '@vue/apollo-composable';

import { GET_PLANS } from '@/graphql/queries/plans';

const {
    result,
    loading
} = useQuery(
    GET_PLANS
);

const plans = computed(
    () => result.value?.plans ?? []
);
</script>