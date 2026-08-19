<script setup>
import { computed } from 'vue';
import { useQuery } from '@vue/apollo-composable';

import {
    GET_USER_PACKAGE
} from '@/graphql/queries/user-packages';

const props = defineProps({
    id: String
});

const {
    result,
    loading
} = useQuery(
    GET_USER_PACKAGE,
    () => ({
        id: props.id
    })
);

const userPackage = computed(
    () =>
        result.value?.userPackage
);

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

    <div
        v-if="loading"
        class="p-6"
    >
        Loading...
    </div>

    <div
        v-else-if="userPackage"
        class="space-y-6"
    >

        <div
            class="bg-white rounded-lg shadow p-6"
        >

            <div
                class="flex items-center justify-between"
            >

                <div>

                    <h1
                        class="text-2xl font-bold"
                    >
                        User Package
                        #{{ userPackage.id }}
                    </h1>

                    <p
                        class="text-gray-500"
                    >
                        Package subscription details
                    </p>

                </div>

                <span
                    class="px-3 py-1 rounded-full text-sm"
                    :class="
                        getStatusClass(
                            userPackage.status
                        )
                    "
                >
                    {{
                        userPackage.status
                    }}
                </span>

            </div>

        </div>

        <div
            class="bg-white rounded-lg shadow p-6"
        >

            <h2
                class="text-lg font-semibold mb-4"
            >
                Subscription Information
            </h2>

            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-4"
            >

                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Email
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{
                            userPackage.email
                        }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Package
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{
                            userPackage.plan_name
                        }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Duration
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{
                            userPackage.duration_days
                        }}
                        days
                    </p>
                </div>

                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Started At
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{
                            userPackage.started_at
                        }}
                    </p>
                </div>

                <div>
                    <p
                        class="text-sm text-gray-500"
                    >
                        Expired At
                    </p>

                    <p
                        class="font-medium"
                    >
                        {{
                            userPackage.expired_at
                        }}
                    </p>
                </div>

            </div>

        </div>

    </div>

</template>