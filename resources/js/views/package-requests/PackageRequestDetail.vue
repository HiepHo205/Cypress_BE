<script setup>
import { computed } from 'vue';
import { useMutation } from '@vue/apollo-composable';
import { useQuery } from '@vue/apollo-composable';

import { GET_PACKAGE_REQUEST } from '@/graphql/queries/package-requests';

import {
    APPROVE_PACKAGE_REQUEST,
    REJECT_PACKAGE_REQUEST
} from '@/graphql/mutations/package-requests';

const props = defineProps({
    id: String
});

const { result, loading, refetch } = useQuery(GET_PACKAGE_REQUEST, () => ({
    id: props.id
}));

const request = computed(() => result.value?.packageRequest);

const { mutate: approve } = useMutation(APPROVE_PACKAGE_REQUEST);

const { mutate: reject } = useMutation(REJECT_PACKAGE_REQUEST);

async function handleApprove() {
    await approve({
        id: props.id
    });

    await refetch();
}

async function handleReject() {
    await reject({
        id: props.id
    });

    await refetch();
}

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
    <div v-if="loading" class="p-6">Loading...</div>

    <div v-else-if="request" class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        Package Request #{{ request.id }}
                    </h1>

                    <p class="text-gray-500 mt-1">Package registration</p>
                </div>

                <span
                    class="px-3 py-1 rounded-full text-sm"
                    :class="getStatusClass(request.status)"
                >
                    {{ request.status }}
                </span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Customer Information</h2>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-500 text-sm">Full Name</p>

                    <p>
                        {{ request.full_name }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Email</p>

                    <p>
                        {{ request.email }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Phone</p>

                    <p>
                        {{ request.phone }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Package</p>

                    <p>
                        {{ request.plan_name || request.plan_id || '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Company</p>

                    <p>
                        {{ request.company_name || '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div
            v-if="request.status === 'pending'"
            class="bg-white rounded-lg shadow p-6"
        >
            <div class="flex gap-3">
                <button
                    @click="handleApprove"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg"
                >
                    Approve
                </button>

                <button
                    @click="handleReject"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg"
                >
                    Reject
                </button>
            </div>
        </div>
    </div>
</template>
