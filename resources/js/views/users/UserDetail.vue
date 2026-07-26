<template>
    <div class="max-w-4xl mx-auto">

        <div class="mb-6">
            <router-link
                to="/admin/users"
                class="text-blue-600 hover:text-blue-800"
            >
                ← Back to users
            </router-link>

            <h1 class="text-2xl font-bold mt-2">
                User Detail
            </h1>
        </div>

        <div
            v-if="loading"
            class="text-center py-10"
        >
            Loading...
        </div>

        <div
            v-else-if="user"
            class="space-y-6"
        >

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">
                    User Information
                </h2>

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <p class="text-gray-500 text-sm">
                            Name
                        </p>

                        <p class="font-medium">
                            {{ user.name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-sm">
                            Email
                        </p>

                        <p class="font-medium">
                            {{ user.email }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-sm">
                            Status
                        </p>

                        <p class="font-medium">
                            {{ user.status }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-sm">
                            Role
                        </p>

                        <p class="font-medium">
                            {{ user.role?.role_name }}
                        </p>
                    </div>

                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">

                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">
                        Permissions
                    </h2>
                </div>

                <div class="space-y-3">

                    <label
                        v-for="permission in permissions"
                        :key="permission.id"
                        class="flex items-center gap-3 border rounded p-3 hover:bg-gray-50"
                    >
                        <input
                            type="checkbox"
                            :value="permission.id"
                            v-model="selectedPermissions"
                        />

                        <div>
                            <p class="font-medium">
                                {{ permission.code }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ permission.description }}
                            </p>
                        </div>
                    </label>

                </div>

                <div class="mt-6">
                    <button v-if="hasPermission('permission.assign')"
                        @click="savePermissions"
                        :disabled="saving"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                    >
                        {{ saving ? 'Saving...' : 'Save Permissions' }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useQuery, useMutation } from '@vue/apollo-composable';

import { GET_USER } from '@/graphql/queries/users';
import { GET_PERMISSIONS } from '@/graphql/queries/permissions';

import {
    ASSIGN_PERMISSION,
    UNASSIGN_PERMISSION
} from '@/graphql/mutations/permissions';

const props = defineProps({
    id: {
        type: String,
        required: true
    }
});

const selectedPermissions = ref([]);
const saving = ref(false);

const {
    result: userResult,
    loading
} = useQuery(
    GET_USER,
    () => ({
        id: props.id
    })
);

const {
    result: permissionResult
} = useQuery(GET_PERMISSIONS);

const {
    mutate: assignPermission
} = useMutation(ASSIGN_PERMISSION);

const {
    mutate: removePermission
} = useMutation(UNASSIGN_PERMISSION);

const user = computed(
    () => userResult.value?.user
);

const currentUser = JSON.parse(
    localStorage.getItem('user') || '{}'
);

function hasPermission(permission) {
    return currentUser.permissions?.some(
        p => p.code === permission
    );
}

const permissions = computed(
    () => permissionResult.value?.permissions ?? []
);

watch(
    user,
    value => {

        if (!value) {
            return;
        }

        selectedPermissions.value =
            value.permissions.map(
                permission => Number(permission.id)
            );
    },
    {
        immediate: true
    }
);

async function savePermissions() {

    saving.value = true;

    try {

        const currentPermissions =
            user.value.permissions.map(
                permission => Number(permission.id)
            );

        const permissionsToAssign =
            selectedPermissions.value.filter(
                id => !currentPermissions.includes(id)
            );

        const permissionsToRemove =
            currentPermissions.filter(
                id => !selectedPermissions.value.includes(id)
            );

        for (const permissionId of permissionsToAssign) {

            await assignPermission({
                userId: props.id,
                permissionId
            });
        }

        for (const permissionId of permissionsToRemove) {

            await removePermission({
                userId: props.id,
                permissionId
            });
        }

        alert('Permissions updated successfully');

    } finally {

        saving.value = false;
    }
}
</script>