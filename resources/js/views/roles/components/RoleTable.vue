<template>
    <div v-if="loading" class="text-gray-500 py-6 text-center">
        Loading roles...
    </div>

    <div v-else-if="error" class="text-red-500 py-6 text-center">
        {{ error.message }}
    </div>

    <div v-else class="overflow-hidden rounded-xl bg-white shadow-none">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-[#6C9ADB] to-[#2B71D3] text-white">
                    <th class="px-5 py-3 text-left">User</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Role</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Permissions</th>
                    <th class="px-5 py-3 text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                <template v-for="role in roles" :key="role.id">
                    <tr v-for="user in role.users" :key="user.id" class="border-b border-gray-200 hover:bg-gray-50">

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold flex items-center justify-center">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <span class="font-medium">{{ user.name }}</span>
                            </div>
                        </td>

                        <td class="px-5 text-gray-600">
                            {{ user.email }}
                        </td>

                        <td class="px-5">
                            <template v-if="editingUserId === user.id">
                                <select v-model="selectedRole" class="border rounded-lg px-3 py-2">
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </template>

                            <template v-else>
                                <span class="px-3 py-1 rounded-full text-white text-xs" :class="roleColor(role.name)">
                                    {{ role.name }}
                                </span>
                            </template>
                        </td>

                        <td class="px-5">
                            <span class="px-3 py-1 rounded-full text-xs"
                                :class="user.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                                {{ user.status }}
                            </span>
                        </td>

                        <td class="px-5">
                            <div v-if="role.permissions?.length" class="space-y-1">
                                <div v-for="permission in role.permissions" :key="permission.id">
                                    <div class="text-blue-600 font-medium">{{ permission.code }}</div>
                                    <div class="text-gray-500 text-xs">{{ permission.description }}</div>
                                </div>
                            </div>

                            <span v-else class="italic text-gray-400">
                                No permission
                            </span>
                        </td>

                        <td class="px-5">
                            <div class="flex justify-center gap-2">
                                <template v-if="editingUserId === user.id">
                                    <button @click="save(user)" class="bg-green-500 text-white px-3 py-1 rounded">
                                        Save
                                    </button>
                                    <button @click="cancel" class="bg-gray-500 text-white px-3 py-1 rounded">
                                        Cancel
                                    </button>
                                </template>

                                <template v-else>
                                    <button @click="edit(user, role)"
                                        class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded transition">
                                        <Pencil :size="16" />
                                    </button>

                                    <button @click="handleDelete(user, role)"
                                        class="flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition">
                                        <Trash2 :size="16" />
                                    </button>
                                </template>
                            </div>
                        </td>

                    </tr>
                </template>

                <tr v-if="roles.length === 0">
                    <td colspan="6" class="py-8 text-center text-gray-400">
                        No roles found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { roleColor } from "@/utils/role";
import { Pencil, Trash2 } from "lucide-vue-next";

defineProps({
    roles: {
        type: Array,
        default: () => []
    },
    loading: Boolean,
    error: Object
});

const emit = defineEmits([
    "update",
    "delete"
]);

const editingUserId = ref(null);
const selectedRole = ref("");

function edit(user, role) {
    editingUserId.value = user.id;
    selectedRole.value = role.name;
}

function cancel() {
    editingUserId.value = null;
    selectedRole.value = "";
}

function save(user) {
    emit("update", {
        userId: user.id,
        role: selectedRole.value
    });

    editingUserId.value = null;
}

function handleDelete(user, role) {
    console.log(user);
    console.log(role);
    if (role.name === "admin") {
        alert("Admin role cannot be deleted.");
        return;
    }

    if (confirm(`Are you sure you want to remove user ${user.name}?`)) {
        emit("delete", user);
    }
}
</script>