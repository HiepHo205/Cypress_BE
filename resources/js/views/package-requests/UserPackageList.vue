<script setup>
import { useRouter } from 'vue-router';
import { useQuery } from '@vue/apollo-composable';

import {
    GET_USER_PACKAGES
} from '@/graphql/queries/user-packages';

const router = useRouter();

const {
    result,
    loading
} = useQuery(
    GET_USER_PACKAGES
);

function viewDetail(id) {
    router.push(
        `/admin/user-packages/${id}`
    );
}

function getStatusClass(status) {

    switch (status) {

        case 'active':
            return 'bg-green-100 text-green-700';

        case 'expired':
            return 'bg-red-100 text-red-700';

        default:
            return 'bg-gray-100 text-gray-700';
    }
}
</script>

<template>
    <div class="p-6">

        <div class="mb-6">
            <h1
                class="text-2xl font-bold"
            >
                User Packages
            </h1>
        </div>

        <div
            class="bg-white rounded-lg shadow overflow-hidden"
        >

            <table
                class="w-full"
            >
                <thead
                    class="bg-gray-50"
                >
                    <tr>
                        <th
                            class="px-6 py-3 text-left"
                        >
                            Email
                        </th>

                        <th
                            class="px-6 py-3 text-left"
                        >
                            Package
                        </th>

                        <th
                            class="px-6 py-3 text-left"
                        >
                            Duration
                        </th>

                        <th
                            class="px-6 py-3 text-left"
                        >
                            Expired At
                        </th>

                        <th
                            class="px-6 py-3 text-left"
                        >
                            Status
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <tr
                        v-if="loading"
                    >
                        <td
                            colspan="5"
                            class="p-6 text-center"
                        >
                            Loading...
                        </td>
                    </tr>

                    <tr
                        v-for="
                            item in result?.userPackages || []
                        "
                        :key="item.id"
                        class="border-t hover:bg-gray-50 cursor-pointer"
                        @click="
                            viewDetail(
                                item.id
                            )
                        "
                    >

                        <td
                            class="px-6 py-4"
                        >
                            {{
                                item.email
                                    ?.length > 30
                                    ? item.email.slice(
                                          0,
                                          30
                                      ) + '...'
                                    : item.email
                            }}
                        </td>

                        <td
                            class="px-6 py-4"
                        >
                            {{
                                item.plan_name
                            }}
                        </td>

                        <td
                            class="px-6 py-4"
                        >
                            {{
                                item.duration_days
                            }}
                            days
                        </td>

                        <td
                            class="px-6 py-4"
                        >
                            {{
                                item.expired_at
                            }}
                        </td>

                        <td
                            class="px-6 py-4"
                        >
                            <span
                                class="px-2 py-1 rounded-full text-xs"
                                :class="
                                    getStatusClass(
                                        item.status
                                    )
                                "
                            >
                                {{
                                    item.status
                                }}
                            </span>
                        </td>

                    </tr>

                </tbody>
            </table>

        </div>

    </div>
</template>