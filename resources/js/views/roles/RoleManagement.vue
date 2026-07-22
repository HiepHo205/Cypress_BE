<script setup>
import { ref, computed, watch } from "vue";
import { useQuery } from "@vue/apollo-composable";
import { Plus } from "lucide-vue-next";
import { GET_ROLES } from "@/graphql/queries/role";
import RoleTable from "./components/RoleTable.vue";
import RolePagination from "./components/RolePagination.vue";
import { useRolePagination } from "@/composables/role/useRolePagination";
import { useRoleManagement } from "@/composables/role/useRoleManagement";
const roles = ref([]);
const search = ref("");
const roleTableRef = ref(null);
const {
  createRole,
  updateRole,
  deleteRole
} = useRoleManagement(roles);

const { result, loading, error } = useQuery(GET_ROLES);
watch(result, (data) => {
  if (!data?.roles) return;

  roles.value = [...data.roles].sort((a, b) => {
    if (a.role_name === "admin") return -1;
    if (b.role_name === "admin") return 1;

    return a.role_name.localeCompare(b.role_name);
  });
}, { immediate: true });
const filteredRoles = computed(() => {
  const keyword = search.value.toLowerCase();
  return roles.value.filter(role =>
    (role.role_name || "")
      .toLowerCase()
      .includes(keyword)
  );

});
const { currentPage, totalPages, paginatedItems, nextPage, prevPage
} = useRolePagination(filteredRoles, 10);

watch(search, () => {
  currentPage.value = 1;
});
function handleAddRole() {

  roleTableRef.value?.addRole();

}

</script>
<template>
  <div class="p-6 bg-white rounded-xl shadow-sm">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold">Role Management</h2>
      </div>

      <button @click="handleAddRole"
        class="flex items-center gap-2 rounded-lg bg-[#2B71D3] px-4 py-2 text-white transition hover:bg-[#1E5BB8]">
        <Plus :size="18" />
        <span>Add Role</span>
      </button>
    </div>

    <div class="mb-5">
      <input v-model="search" type="text" placeholder="Search role..."
        class="w-full px-4 py-2 border border-gray-200 rounded-lg" />
    </div>

    <div v-if="loading" class="text-gray-500">
      Loading roles...
    </div>

    <div v-else-if="error" class="text-red-500">
      {{ error.message }}
    </div>

    <div v-else class="overflow-hidden bg-white rounded-xl shadow-sm">

      <RoleTable ref="roleTableRef" :roles="paginatedItems" :role-options="roles" :loading="loading" :error="error"
        @create="createRole" @update="updateRole" @delete="deleteRole" />
      <RolePagination :current-page="currentPage" :total-pages="totalPages" @prev="prevPage" @next="nextPage" />

    </div>
  </div>
</template>
