<script setup>
import { computed } from 'vue';
import { useQuery } from '@vue/apollo-composable';

import { useMutation } from '@vue/apollo-composable';

import {
    GET_SERVICE_REQUEST
} from '@/graphql/queries/service-requests';

import {
    APPROVE_SERVICE_REQUEST,
    REJECT_SERVICE_REQUEST
} from '@/graphql/mutations/service-requests';

const props = defineProps({
    id: String
});

const {
    result,
    loading,
    refetch
} = useQuery(
    GET_SERVICE_REQUEST,
    () => ({
        id: props.id
    })
);

const {
    mutate: approveRequest
} = useMutation(
    APPROVE_SERVICE_REQUEST
);

const {
    mutate: rejectRequest
} = useMutation(
    REJECT_SERVICE_REQUEST
);

const request = computed(
    () =>
        result.value?.serviceRequest
);

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

async function handleApprove()
{
    try {

        await approveRequest({
            id: props.id
        });

        await result.refetch?.();

        location.reload();

    } catch (error) {

        console.error(error);

        alert(
            'Approve failed'
        );
    }
}

async function handleReject()
{
    try {

        await rejectRequest({
            id: props.id
        });

        await result.refetch?.();

        location.reload();

    } catch (error) {

        console.error(error);

        alert(
            'Reject failed'
        );
    }
}
</script>

<template>
    <div
        v-if="loading"
        class="p-6"
    >
        Loading...
    </div>

    <div
        v-else-if="request"
        class="space-y-6"
    >

        <!-- Header -->
        <div
            class="bg-white rounded-lg shadow p-6"
        >
            <div
                class="flex items-center justify-between"
            >
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900"
                    >
                        Service Request #{{ request.id }}
                    </h1>

                    <p
                        class="text-gray-500 mt-1"
                    >
                        Client request details
                    </p>
                </div>

                <span
                    class="px-3 py-1 rounded-full text-sm font-medium"
                    :class="
                        getStatusClass(
                            request.status
                        )
                    "
                >
                    {{ request.status }}
                </span>
            </div>
        </div>

        <!-- Contact Information -->
        <div
            class="bg-white rounded-lg shadow p-6"
        >
            <h2
                class="text-lg font-semibold mb-4"
            >
                Contact Information
            </h2>

            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-4"
            >
                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Full Name
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{ request.full_name || '-' }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Email
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{ request.email || '-' }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Phone
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{ request.phone || '-' }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Request Type
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{ request.request_type || '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Company Information -->
        <div
            class="bg-white rounded-lg shadow p-6"
        >
            <h2
                class="text-lg font-semibold mb-4"
            >
                Company Information
            </h2>

            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-4"
            >
                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Company Name
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{ request.company_name || '-' }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Service Interest
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{ request.service_interest || '-' }}
                    </p>
                </div>

                <div
                    class="md:col-span-2"
                >
                    <p
                        class="text-sm text-gray-500"
                    >
                        Address
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{ request.address || '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Message -->
        <div
            class="bg-white rounded-lg shadow p-6"
        >
            <h2
                class="text-lg font-semibold mb-4"
            >
                Message
            </h2>

            <div
                class="bg-gray-50 rounded-lg p-4 whitespace-pre-wrap"
            >
                {{ request.message || 'No message provided' }}
            </div>
        </div>

        <!-- Actions -->
        <div
                v-if="
                    request.status ==='pending'
                "
            class="bg-white rounded-lg shadow p-6"
        >
            <div

                class="bg-white rounded-lg shadow p-6"
            >
                <div class="flex gap-3">
                    <button
                        @click="handleApprove"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    >
                        Approve
                    </button>

                    <button
                        @click="handleReject"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Reject
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>