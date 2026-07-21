<template>

  <div class="p-6 bg-white rounded-xl shadow-sm">


    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6">


      <div>

        <h2 class="text-2xl font-bold">
          Role Management
        </h2>


        <p class="text-sm text-gray-500">
          Manage roles and permissions
        </p>

      </div>



      <button class="px-4 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">

        + Add Role

      </button>


    </div>




    <!-- SEARCH -->
    <div class="mb-5">

      <input v-model="search" type="text" placeholder="Search role..." class="w-full px-4 py-2 border rounded-lg" />

    </div>




    <!-- LOADING -->
    <div v-if="loading" class="text-gray-500">

      Loading roles...

    </div>




    <!-- ERROR -->
    <div v-else-if="error" class="text-red-500">

      {{ error.message }}

    </div>




    <!-- TABLE -->
    <div v-else class="overflow-hidden bg-white border rounded-xl shadow-sm">


      <RoleTable :roles="paginatedItems" :loading="loading" :error="error" @update="updateRoleData"
        @delete="deleteRole" />



      <RolePagination :current-page="currentPage" :total-pages="totalPages" @prev="prevPage" @next="nextPage"
        @change="goToPage" />



    </div>


  </div>

</template>




<script setup>


import {
  ref,
  computed,
  watch
}
  from "vue";


import {
  useQuery,
  useMutation
}
  from "@vue/apollo-composable";


import {
  useToast
}
  from "vue-toastification";



import {
  GET_ROLES
}
  from "@/graphql/queries/role";



import {
  DELETE_ROLE,
  CHANGE_USER_ROLE
}
  from "@/graphql/mutations/role";



import RoleTable from "./components/RoleTable.vue";

import RolePagination from "./components/RolePagination.vue";

import {
  useRolePagination
}
  from "@/composables/useRolePagination";





const toast = useToast();




const roles = ref([]);

const search = ref("");






// GET ROLES

const {

  result,

  loading,

  error,

  refetch

}
  =
  useQuery(
    GET_ROLES,
    null,
    {
      fetchPolicy: "network-only"
    }
  );





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







// SEARCH

const filteredRoles = computed(() => {


  return roles.value.filter(role =>


    role.name

      .toLowerCase()

      .includes(
        search.value.toLowerCase()
      )


  );


});








// PAGINATION

const {

  currentPage,

  totalPages,

  paginatedItems,

  nextPage,

  prevPage,

  goToPage

}
  =
  useRolePagination(filteredRoles);









// MUTATION

const {

  mutate: changeUserRole

}
  =
  useMutation(
    CHANGE_USER_ROLE
  );





const {

  mutate: removeRole

}
  =
  useMutation(
    DELETE_ROLE
  );









// UPDATE ROLE USER

async function updateRoleData(payload) {


  try {


    await changeUserRole(
      {
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









// DELETE ROLE

async function deleteRole(role) {



  if (
    role.name === "admin"
    ||
    role.name === "user"
  ) {


    toast.warning(
      "Default roles cannot be deleted."
    );


    return;

  }






  if (
    !confirm(
      `Delete ${role.name}?`
    )
  ) {

    return;

  }







  try {


    await removeRole({

      id: role.id

    });




    await refetch();




    toast.success(
      "Role deleted successfully"
    );



  }
  catch (err) {


    toast.error(

      err?.graphQLErrors?.[0]?.message

      ||

      err.message

      ||

      "Delete failed"

    );


  }



}





</script>