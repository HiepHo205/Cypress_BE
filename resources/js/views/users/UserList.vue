<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">User Management</h1>

    <div v-if="loading" class="text-center">Loading...</div>
    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
      <strong class="font-bold">Error!</strong>
      <span class="block sm:inline">{{ error.message }}</span>
    </div>

    <div v-if="result && result.users" class="overflow-x-auto bg-white rounded-lg shadow">
      <table class="min-w-full">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="user in result.users.data" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ user.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ new Date(user.created_at).toLocaleDateString() }}</td>
          </tr>
        </tbody>
      </table>
      <!-- Pagination sẽ được thêm ở đây -->
    </div>
  </div>
</template>

<script setup>
import { useQuery } from '@vue/apollo-composable'
import gql from 'graphql-tag'

// Định nghĩa câu truy vấn GraphQL để lấy danh sách người dùng
// Lưu ý: Tên query 'users' và các trường (id, name, email)
// phải khớp với schema GraphQL của bạn.
const USERS_QUERY = gql`
  query GetUsers($page: Int) {
    users(page: $page) {
      data {
        id
        name
        email
        created_at
      }
      paginatorInfo {
        currentPage
        lastPage
      }
    }
  }
`;

const { result, loading, error } = useQuery(USERS_QUERY, {
  page: 1, // Lấy trang đầu tiên
});

</script>