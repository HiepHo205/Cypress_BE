<template>
  <div class="max-w-lg">
    <div class="mb-6">
      <router-link to="/admin/users" class="text-sm text-blue-600 hover:text-blue-800">
        &larr; Back to users
      </router-link>
      <h1 class="text-2xl font-bold mt-2">Edit User</h1>
    </div>

    <div v-if="queryLoading" class="text-center py-8 text-gray-500">Loading...</div>

    <form
      v-else-if="formReady"
      class="bg-white rounded-lg shadow p-6 space-y-4"
      @submit.prevent="submit"
    >
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
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
          New Password <span class="text-gray-400 font-normal">(leave blank to keep current)</span>
        </label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          minlength="8"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select
          id="status"
          v-model="form.status"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="active">active</option>
          <option value="unactive">unactive</option>
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
        :disabled="mutationLoading"
      >
        {{ mutationLoading ? 'Saving...' : 'Save Changes' }}
      </button>
    </form>

    <div
      v-else-if="queryErrorMessages.length"
      class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded space-y-1"
    >
      <p v-for="message in queryErrorMessages" :key="message">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useMutation, useQuery } from '@vue/apollo-composable';
import { GET_USER, GET_USERS } from '@/graphql/queries/users';
import { UPDATE_USER } from '@/graphql/mutations/users';
import { getGraphQLErrorMessages } from '@/utils/graphqlErrors';

const props = defineProps({
  id: {
    type: String,
    required: true,
  },
});

const router = useRouter();

const form = reactive({
  name: '',
  email: '',
  password: '',
  status: 'active',
});

const { result, loading: queryLoading, error: queryError } = useQuery(GET_USER, () => ({
  id: props.id,
}));

const formReady = computed(() => Boolean(result.value?.user));
const queryErrorMessages = computed(() => getGraphQLErrorMessages(queryError.value));

watch(
  () => result.value?.user,
  (user) => {
    if (!user) {
      return;
    }

    form.name = user.name;
    form.email = user.email;
    form.status = user.status;
    form.password = '';
  },
  { immediate: true },
);

const {
  mutate: updateUser,
  loading: mutationLoading,
  error: mutationError,
} = useMutation(UPDATE_USER, {
  refetchQueries: [
    { query: GET_USERS, variables: { page: 1 } },
    { query: GET_USER, variables: { id: props.id } },
  ],
});

const errorMessages = computed(() => getGraphQLErrorMessages(mutationError.value));

async function submit() {
  const input = {
    name: form.name,
    email: form.email,
    status: form.status,
  };

  if (form.password) {
    input.password = form.password;
  }

  try {
    await updateUser({ id: props.id, input });
    router.push({ name: 'users.list' });
  } catch {
    // Validation errors are shown via errorMessages.
  }
}
</script>
