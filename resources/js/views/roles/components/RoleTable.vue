<template>
    <div v-if="loading" class="text-gray-500">
        Loading roles...
    </div>

    <div v-else-if="error" class="text-red-500">
        {{ error.message }}
    </div>

    <div v-else class="overflow-hidden rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-[#6C9ADB] to-[#2B71D3] text-white">
                    <th class="text-left py-3 px-4 font-semibold">User</th>
                    <th class="text-left px-4 font-semibold">Email</th>
                    <th class="text-left px-4 font-semibold">Role</th>
                    <th class="text-left px-4 font-semibold">Status</th>
                    <th class="text-left px-4 font-semibold">Permissions</th>
                    <th class="text-center px-4 font-semibold">Action</th>
                </tr>
            </thead>

            <tbody>
                <template v-for="role in roles" :key="role.id">
                    <tr v-for="user in role.users || []" :key="user.id"
                        class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                                    {{ user.name ? user.name.charAt(0).toUpperCase() : "U" }}
                                </div>

                                <p class="font-medium text-gray-800">
                                    {{ user.name }}
                                </p>
                            </div>
                        </td>

                        <td class="px-4 text-gray-600">
                            {{ user.email }}
                        </td>

                        <td class="px-4">
                            <template v-if="editingRoleId === role.id">
                                <select v-model="form.name"
                                    class="w-36 border border-gray-300 rounded-lg px-2 py-1 bg-white outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </template>

                            <template v-else>
                                <span class="px-3 py-1 rounded-full text-xs text-white" :class="roleColor(role.name)">
                                    {{ role.name }}
                                </span>
                            </template>
                        </td>

                        <td class="px-4">
                            <span class="px-3 py-1 rounded-full text-xs" :class="user.status === 'active'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-500'
                                ">
                                {{ user.status || "inactive" }}
                            </span>
                        </td>

                        <td class="px-4">
                            <div class="flex flex-wrap gap-2">
                                <div v-for="permission in role.permissions || []" :key="permission.id" class="text-xs">
                                    <span class="font-medium text-blue-600">
                                        {{ permission.code }}
                                    </span>

                                    <br>

                                    <span class="text-gray-500">
                                        {{ permission.description }}
                                    </span>
                                </div>

                                <span v-if="!role.permissions?.length" class="text-gray-400 text-xs italic">
                                    No permissions
                                </span>
                            </div>
                        </td>

                        <td class="px-4">
                            <div class="flex justify-center gap-2">
                                <template v-if="editingRoleId === role.id">
                                    <button @click="saveRole(role)"
                                        class="w-8 h-8 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center">
                                        <Check :size="16" />
                                    </button>

                                    <button @click="cancelEdit"
                                        class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 flex items-center justify-center">
                                        <X :size="16" />
                                    </button>
                                </template>

                                <template v-else>
                                    <button @click="startEdit(role)"
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center">
                                        <Pencil :size="16" />
                                    </button>

                                    <button @click="$emit('delete', role)"
                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center">
                                        <Trash2 :size="16" />
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>

                <tr v-if="roles.length === 0">
                    <td colspan="6" class="text-center py-6 text-gray-400">
                        No roles found
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Pencil, Trash2, Check, X } from "lucide-vue-next";
import { roleColor } from "@/utils/role";

const props = defineProps({
    roles: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["update", "delete"]);

const editingRoleId = ref(null);

const form = ref({
    name: "",
});

function startEdit(role) {
    editingRoleId.value = role.id;
    form.value.name = role.name;
}

function cancelEdit() {
    editingRoleId.value = null;
    form.value.name = "";
}

function saveRole(role) {
    emit("update", {
        id: role.id,
        name: form.value.name,
    });

    cancelEdit();
}
</script>