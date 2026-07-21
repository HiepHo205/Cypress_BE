<template>
  <div class="p-6 bg-white rounded-xl shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-2xl font-bold"> Role Management</h2>
        <p class="text-sm text-gray-500">
          Manage roles and permissions
        </p>
      </div>
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
      <RoleTable :roles="paginatedItems" :loading="loading" :error="error" @update="updateRoleData"
        @delete="removeUserRole" />
      <RolePagination :current-page="currentPage" :total-pages="totalPages" @prev="prevPage" @next="nextPage"
        @change="goToPage" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { useQuery, useMutation } from "@vue/apollo-composable";
import { useToast } from "vue-toastification";
import { GET_ROLES } from "@/graphql/queries/role";
import { DELETE_ROLE, CHANGE_USER_ROLE } from "@/graphql/mutations/role";
import RoleTable from "./components/RoleTable.vue";
import RolePagination from "./components/RolePagination.vue";
import { useRolePagination } from "@/composables/useRolePagination";
const toast = useToast();
const roles = ref([]);
const search = ref("");
const { result, loading, error, refetch } = useQuery(GET_ROLES, null, { fetchPolicy: "network-only" });
watch(
  result,
  (data) => {
    if (data?.roles) {
      roles.value = data.roles;
    }
  },
  {
    immediate: true
  }
);
const filteredRoles = computed(() => {
  return roles.value.filter(role =>
    role.name.toLowerCase()
      .includes(
        search.value.toLowerCase()
      )
  );
});
const { currentPage, totalPages, paginatedItems, nextPage, prevPage, goToPage } = useRolePagination(filteredRoles);
const { mutate: changeUserRole } = useMutation(CHANGE_USER_ROLE);
const { mutate: removeUserFromRole } = useMutation(DELETE_ROLE);
async function updateRoleData(payload) {
  try {
    await changeUserRole({
      userId: payload.userId,
      role: payload.role
    },
      {
        context: {
          headers: {
            Authorization:
              `Bearer ${localStorage.getItem("token")}`
          }
        }
      }
    );
    await refetch();
    toast.success(
      "Role updated successfully"
    );
  }
  catch (err) {
    console.error(err);
    toast.error(
      err?.graphQLErrors?.[0]?.message
      ||
      err.message
      ||
      "Update role failed"
    );
  }
}
async function removeUserRole(user) {
  try {
    await removeUserFromRole(
      {
        id: user.id
      },
      {
        context: {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
          }
        }
      }
    );

    await refetch();

    toast.success("User removed successfully");
  } catch (err) {
    console.error(err);
    toast.error(
      err?.graphQLErrors?.[0]?.message ||
      err.message ||
      "Remove user failed"
    );
  }
}
</script>