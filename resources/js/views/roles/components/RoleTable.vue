<template>
    <div v-if="loading" class="text-gray-500 py-6 text-center">
        Loading roles...
    </div>
    <div v-else-if="error" class="text-red-500 py-6 text-center">
        {{ error.message }}
    </div>
    <div v-else class="overflow-hidden rounded-xl bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-[#6C9ADB] to-[#2B71D3] text-white">
                    <th class="px-5 py-3 text-left">User</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Role</th>
                    <th class="px-5 py-3 text-left">Permissions</th>
                    <th class="px-5 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id" class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-600">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>

                            <span class="font-medium">
                                {{ user.name }}
                            </span>

                        </div>
                    </td>
                    <td class="px-5 text-gray-600">
                        {{ user.email }}
                    </td>
                    <td class="px-5">
                        <template v-if="editingUserId === user.id">

                            <select v-model="selectedRole" class="rounded-lg border px-3 py-2">
                                <option value="admin">
                                    Admin
                                </option>

                                <option value="user">
                                    User
                                </option>
                            </select>
                        </template>
                        <template v-else>

                            <span class="rounded-full px-3 py-1 text-xs text-white" :class="roleColor(user.role.name)">
                                {{ user.role.name }}
                            </span>
                        </template>
                    </td>
                    <td class="px-5">
                        <div v-if="user.role.permissions?.length" class="space-y-1">
                            <div v-for="permission in user.role.permissions" :key="permission.id">
                                <p class="font-medium text-blue-600">
                                    {{ permission.code }}
                                </p>

                                <p class="text-xs text-gray-500">
                                    {{ permission.description }}
                                </p>
                            </div>
                        </div>
                        <span v-else class="italic text-gray-400">
                            No permission
                        </span>
                    </td>
                    <td class="px-5">
                        <div class="flex justify-center gap-2">
                            <template v-if="editingUserId === user.id">
                                <button @click="save(user)"
                                    class="rounded bg-green-500 px-3 py-1 text-white hover:bg-green-600">
                                    Save
                                </button>
                                <button @click="cancel"
                                    class="rounded bg-gray-500 px-3 py-1 text-white hover:bg-gray-600">
                                    Cancel
                                </button>
                            </template>
                            <template v-else>
                                <button @click="edit(user)"
                                    class="rounded bg-blue-500 p-2 text-white hover:bg-blue-600">
                                    <Pencil :size="16" />
                                </button>
                                <button @click="handleDelete(user)"
                                    class="rounded bg-red-500 p-2 text-white hover:bg-red-600">
                                    <Trash2 :size="16" />
                                </button>
                            </template>
                        </div>
                    </td>
                </tr>
                <tr v-if="users.length === 0">
                    <td colspan="6" class="py-8 text-center text-gray-400">
                        No users found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { Pencil, Trash2 } from "lucide-vue-next";
import { roleColor } from "@/utils/role";
import { useToast } from "vue-toastification";
const toast = useToast();
const props = defineProps({
    users: {
        type: Array,
        default: () => []
    },
    allUsers: {
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
function edit(user) {
    editingUserId.value = user.id;
    selectedRole.value = user.role.name;
}
function cancel() {
    editingUserId.value = null;
    selectedRole.value = "";
}
function save(user) {
    if (selectedRole.value === "admin") {
        const hasAdmin = props.allUsers.some(
            item =>
                item.role?.name === "admin" &&
                item.id !== user.id
        );
        if (hasAdmin) {
            toast.error(
                "There is already an admin. You cannot assign another admin role."
            );
            return;
        }
    }
    emit("update", {
        userId: user.id,
        role: selectedRole.value
    });
    editingUserId.value = null;
}
function handleDelete(user) {
    if (user.role?.name === "admin") {
        toast.warning(
            "Admin role cannot be deleted."
        );
        return;
    }
    if (
        confirm(
            `Are you sure you want to remove user ${user.name}?`
        )
    ) {
        emit(
            "delete",
            user
        );
    }
}
</script>