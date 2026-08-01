<template>
  <div class="max-w-4xl mx-auto">
    <div class="mb-6">
      <router-link to="/admin/users" class="text-sm text-blue-600 hover:text-blue-800">
        &larr; Back to users
      </router-link>
      <h1 class="text-2xl font-bold mt-2">Create User</h1>
    </div>

    <form class="bg-white rounded-lg shadow p-6 space-y-4" @submit.prevent="submit">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          required
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          required
          minlength="8"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <div>
        <label
          for="role"
          class="block text-sm font-medium text-gray-700 mb-1"
        >
          Role
        </label>

        <select
          id="role"
          v-model="form.role_id"
          required
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="" disabled>
            Select a role
          </option>

          <option
            v-for="role in roles"
            :key="role.id"
            :value="role.id"
          >
            {{ role.role_name }}
          </option>
        </select>
      </div>

      <div
        v-if="errorMessages.length"
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm space-y-1"
      >
        <p v-for="message in errorMessages" :key="message">{{ message }}</p>
      </div>

      <button
        type="submit"
        class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50"
        :disabled="loading"
      >
        {{ loading ? 'Creating...' : 'Create User' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { computed, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useMutation, useQuery } from '@vue/apollo-composable';
import { CREATE_USER } from '@/graphql/mutations/users';
import { GET_USERS } from '@/graphql/queries/users';
import { getGraphQLErrorMessages } from '@/utils/graphqlErrors';
import { GET_ROLES } from '@/graphql/queries/role';

const { result: roleResult } = useQuery(GET_ROLES);

const roles = computed(() => roleResult.value?.roles ?? []);

const router = useRouter();

const form = reactive({
  name: '',
  email: '',
  password: '',
  role_id: '',
});

const { mutate: createUser, loading, error } = useMutation(CREATE_USER, {
  refetchQueries: [{ query: GET_USERS, variables: { page: 1 } }],
});

const errorMessages = computed(() => getGraphQLErrorMessages(error.value));

async function submit() {
  try {
    await createUser({
      input: {
        name: form.name,
        email: form.email,
        password: form.password,
        role_id: form.role_id,
      },
    });

    router.push({ name: 'users.list' });
  } catch {
    // Validation errors are shown via errorMessages.
  }
}
</script>
