<template>
  <div class="p-6 bg-white rounded-xl shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-2xl font-bold">Role Management</h2>
        <p class="text-sm text-gray-500">
          Manage roles and permissions
        </p>
      </div>

      <button class="px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
        + Add Role
      </button>
    </div>

    <div class="mb-5">
      <input v-model="search" type="text" placeholder="Search role..."
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" />
    </div>

    <div v-if="loading" class="text-gray-500">
      Loading roles...
    </div>

    <div v-else-if="error" class="text-red-500">
      {{ error.message }}
    </div>

    <div v-else class="overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm">
      <RoleTable :roles="paginatedItems" :loading="loading" :error="error" @update="updateRoleData"
        @delete="deleteRole" />

      <RolePagination :current-page="currentPage" :total-pages="totalPages" @prev="prevPage" @next="nextPage"
        @change="goToPage" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useQuery, useMutation } from "@vue/apollo-composable";

import { GET_ROLES } from "@/graphql/queries/role";
import { DELETE_ROLE, UPDATE_ROLE } from "@/graphql/mutations/role";

import { useRolePagination } from "@/composables/useRolePagination";

import RoleTable from "./components/RoleTable.vue";
import RolePagination from "./components/RolePagination.vue";

const roles = ref([]);
const search = ref("");


const {
  result,
  loading,
  error,
  refetch,
} = useQuery(GET_ROLES, null, {
  fetchPolicy: "network-only",
});

watch(
  result,
  (data) => {
    if (data?.roles) {
      roles.value = data.roles;
    }
  },
  {
    immediate: true,
  }
);

const filteredRoles = computed(() => {
  return roles.value.filter((role) =>
    role.name.toLowerCase().includes(search.value.toLowerCase())
  );
});

const {
  currentPage,
  totalPages,
  paginatedItems,
  nextPage,
  prevPage,
  goToPage,
} = useRolePagination(filteredRoles);

const { mutate: updateRole } = useMutation(UPDATE_ROLE);
const { mutate: removeRole } = useMutation(DELETE_ROLE);

async function updateRoleData(data) {
  await updateRole({
    id: data.id,
    name: data.name,
  });

  await refetch();
}

async function deleteRole(role) {
  if (role.name === "admin" || role.name === "user") {
    alert("Cannot delete default role");
    return;
  }

  if (!confirm(`Delete ${role.name}?`)) {
    return;
  }

  await removeRole({
    id: role.id,
  });

  await refetch();
}
</script>