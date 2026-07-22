<script setup>
import {
    Pencil,
    Trash2,
    Check,
    X
} from "lucide-vue-next";

import { useRoleTable } from "@/composables/role/useRoleTable";

const props = defineProps({
    roles: {
        type: Array,
        default: () => []
    },
    roleOptions: {
        type: Array,
        default: () => []
    },
    loading: Boolean,
    error: Object
});

const emit = defineEmits([
    "create",
    "update",
    "delete"
]);
const {
    creating,
    newRole,
    editingRoleId,
    editedName,
    editedDescription,
    addRole,
    saveCreate,
    cancelCreate,
    edit,
    save,
    cancel,
    handleDelete
} = useRoleTable(emit);

defineExpose({
    addRole
});
</script>
<template>
    <div v-if="loading" class="py-6 text-center text-gray-500">
        Loading roles...
    </div>

    <div v-else-if="error" class="py-6 text-center text-red-500">
        {{ error.message }}
    </div>

    <div v-else class="overflow-hidden rounded-xl bg-white">
        <table class="w-full table-fixed text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-[#6C9ADB] to-[#2B71D3] text-white">
                    <th class="w-1/4 px-5 py-3 text-center">
                        Name
                    </th>

                    <th class="w-2/4 border-l border-blue-300 px-5 py-3 text-center">
                        Description
                    </th>

                    <th class="w-1/4 border-l border-blue-300 px-5 py-3 text-center">
                        Action
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr v-if="creating" class="border-b border-gray-200 bg-blue-50">
                    <td class="w-1/4 px-5 py-4 text-center">
                        <input v-model="newRole.name" placeholder="Role name"
                            class="w-full rounded border border-gray-300 px-3 py-2" />
                    </td>

                    <td class="w-2/4 border-l border-gray-200 px-5 py-4">
                        <input v-model="newRole.description" placeholder="Description"
                            class="w-full rounded border border-gray-300 px-3 py-2" />
                    </td>

                    <td class="w-1/4 border-l border-gray-200 px-5 py-4">
                        <div class="flex justify-center gap-2">
                            <button @click="saveCreate" class="rounded bg-green-500 p-2 text-white hover:bg-green-600"
                                title="Create">
                                <Check :size="16" />
                            </button>

                            <button @click="cancelCreate" class="rounded bg-gray-500 p-2 text-white hover:bg-gray-600"
                                title="Cancel">
                                <X :size="16" />
                            </button>
                        </div>
                    </td>
                </tr>

                <tr v-for="role in roles" :key="role.id" class="border-b border-gray-200 hover:bg-gray-50">

                    <td class="w-1/4 px-5 py-4 text-center align-middle">
                        <template v-if="editingRoleId === role.id">
                            <input v-model="editedName" class="w-full rounded border border-gray-300 px-3 py-2" />
                        </template>

                        <template v-else>
                            {{ role.name }}
                        </template>
                    </td>

                    <td class="w-2/4 border-l border-gray-200 px-5 py-4 align-middle">
                        <template v-if="editingRoleId === role.id">
                            <input v-model="editedDescription"
                                class="w-full rounded border border-gray-300 px-3 py-2" />
                        </template>

                        <template v-else>
                            <p class="text-center break-words">
                                {{ role.description || "No description" }}
                            </p>
                        </template>
                    </td>

                    <td class="w-1/4 border-l border-gray-200 px-5 py-4">
                        <div class="flex justify-center gap-2">
                            <template v-if="editingRoleId === role.id">
                                <button @click="save(role)"
                                    class="rounded bg-green-500 p-2 text-white hover:bg-green-600" title="Save">
                                    <Check :size="16" />
                                </button>

                                <button @click="cancel" class="rounded bg-gray-500 p-2 text-white hover:bg-gray-600"
                                    title="Cancel">
                                    <X :size="16" />
                                </button>
                            </template>

                            <template v-else>
                                <button @click="edit(role)"
                                    class="rounded bg-blue-500 p-2 text-white hover:bg-blue-600">
                                    <Pencil :size="16" />
                                </button>

                                <button @click="handleDelete(role)"
                                    class="rounded bg-red-500 p-2 text-white hover:bg-red-600">
                                    <Trash2 :size="16" />
                                </button>
                            </template>
                        </div>
                    </td>
                </tr>

                <tr v-if="roles.length === 0 && !creating">
                    <td colspan="3" class="py-8 text-center text-gray-400">
                        No roles found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
