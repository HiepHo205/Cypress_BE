<template>
    <div v-if="loading" class="text-gray-500 py-6 text-center">
        Loading roles...
    </div>

    <div v-else-if="error" class="text-red-500 py-6 text-center">
        {{ error.message }}
    </div>

    <div v-else class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow">

        <table class="w-full text-sm">

            <thead>
                <tr class="bg-gradient-to-r from-[#6C9ADB] to-[#2B71D3] text-white">

                    <th class="px-5 py-3 text-left">
                        User
                    </th>

                    <th class="px-5 py-3 text-left">
                        Email
                    </th>

                    <th class="px-5 py-3 text-left">
                        Role
                    </th>

                    <th class="px-5 py-3 text-left">
                        Status
                    </th>

                    <th class="px-5 py-3 text-left">
                        Permissions
                    </th>

                    <th class="px-5 py-3 text-center">
                        Action
                    </th>

                </tr>
            </thead>


            <tbody>

                <template v-for="role in roles" :key="role.id">

                    <tr v-for="user in role.users" :key="user.id" class="border-b hover:bg-gray-50">


                        <!-- USER -->
                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold flex items-center justify-center">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>


                                <span class="font-medium">
                                    {{ user.name }}
                                </span>

                            </div>

                        </td>



                        <!-- EMAIL -->
                        <td class="px-5 text-gray-600">

                            {{ user.email }}

                        </td>




                        <!-- ROLE -->
                        <td class="px-5">


                            <template v-if="editingUserId === user.id">


                                <select v-model="selectedRole" class="border rounded-lg px-3 py-2">

                                    <option value="admin">
                                        Admin
                                    </option>

                                    <option value="user">
                                        User
                                    </option>


                                </select>


                            </template>


                            <template v-else>


                                <span class="px-3 py-1 rounded-full text-white text-xs" :class="roleColor(role.name)">

                                    {{ role.name }}

                                </span>


                            </template>


                        </td>





                        <!-- STATUS -->
                        <td class="px-5">


                            <span class="px-3 py-1 rounded-full text-xs" :class="user.status === 'active'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-600'
                                ">

                                {{ user.status }}

                            </span>


                        </td>




                        <!-- PERMISSIONS -->
                        <td class="px-5">


                            <div v-if="role.permissions?.length" class="space-y-1">

                                <div v-for="permission in role.permissions" :key="permission.id">

                                    <div class="text-blue-600 font-medium">

                                        {{ permission.code }}

                                    </div>


                                    <div class="text-gray-500 text-xs">

                                        {{ permission.description }}

                                    </div>


                                </div>


                            </div>


                            <span v-else class="italic text-gray-400">

                                No permission

                            </span>


                        </td>





                        <!-- ACTION -->
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


                                    <button @click="edit(user, role)" class="bg-blue-500 text-white px-3 py-1 rounded">

                                        Edit

                                    </button>



                                    <button @click="$emit('delete', role)"
                                        class="bg-red-500 text-white px-3 py-1 rounded">

                                        Delete

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


    emit(
        "update",
        {
            userId: user.id,
            role: selectedRole.value
        }
    );


    editingUserId.value = null;


}


</script>