<template>
  <div class="p-6 bg-white rounded-xl shadow">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">Danh sách người dùng</h2>

      <div class="flex gap-2">
        <input
          v-model="search"
          type="text"
          placeholder="Tìm kiếm tên, email..."
          class="px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
        />

        <select
          v-model="filterRole"
          class="px-3 py-2 border rounded-md text-sm focus:outline-none"
        >
          <option value="">Tất cả vai trò</option>
          <option value="Administrator">Administrator</option>
          <option value="Editor">Editor</option>
          <option value="Viewer">Viewer</option>
        </select>

        <button
          class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700"
        >
          Thêm người dùng
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
        <thead class="text-gray-500 border-b">
          <tr>
            <th class="py-3">Họ tên & Email</th>
            <th>Vai trò hiện tại</th>
            <th>Ngày tham gia</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="user in filteredUsers"
            :key="user.id"
            class="border-b hover:bg-gray-50"
          >
            <!-- User -->
            <td class="py-3 flex items-center gap-3">
              <img
                :src="user.avatar"
                class="w-10 h-10 rounded-full object-cover"
              />
              <div>
                <div class="font-medium">{{ user.name }}</div>
                <div class="text-gray-500 text-xs">
                  {{ user.email }}
                </div>
              </div>
            </td>

            <!-- Role -->
            <td>
              <span
                class="px-2 py-1 rounded-full text-white text-xs"
                :class="roleClass(user.role)"
              >
                {{ user.role }}
              </span>
            </td>

            <!-- Date -->
            <td>{{ user.joined }}</td>

            <!-- Status -->
            <td>
              <button
                @click="user.active = !user.active"
                class="w-10 h-5 flex items-center rounded-full p-1 transition"
                :class="user.active ? 'bg-green-500' : 'bg-gray-300'"
              >
                <div
                  class="bg-white w-4 h-4 rounded-full shadow transform transition"
                  :class="user.active ? 'translate-x-5' : ''"
                ></div>
              </button>
            </td>

            <!-- Actions -->
            <td class="flex gap-2">
              <button class="text-gray-500 hover:text-blue-500">
                ✏️
              </button>
              <button class="text-gray-500 hover:text-red-500">
                🗑
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

const search = ref("");
const filterRole = ref("");

const users = ref([
  {
    id: 1,
    name: "Lê Hoàng",
    email: "hoang.le@company.com",
    role: "Administrator",
    joined: "12/05/2024",
    active: true,
    avatar: "https://i.pravatar.cc/40?img=1",
  },
  {
    id: 2,
    name: "Sế Chư Adnh",
    email: "hoang.le@company.com",
    role: "Editor",
    joined: "12/05/2024",
    active: true,
    avatar: "https://i.pravatar.cc/40?img=2",
  },
  {
    id: 3,
    name: "Phim Ngam",
    email: "hoang.le@company.com",
    role: "Editor",
    joined: "12/05/2024",
    active: true,
    avatar: "https://i.pravatar.cc/40?img=3",
  },
  {
    id: 4,
    name: "Lê Thần San",
    email: "hoang.le@company.com",
    role: "Editor",
    joined: "12/05/2024",
    active: true,
    avatar: "https://i.pravatar.cc/40?img=4",
  },
  {
    id: 5,
    name: "Sập Hàn Hàn",
    email: "hoang.le@company.com",
    role: "Viewer",
    joined: "12/05/2024",
    active: true,
    avatar: "https://i.pravatar.cc/40?img=5",
  },
]);

const roleClass = (role) => {
  switch (role) {
    case "Administrator":
      return "bg-red-500";
    case "Editor":
      return "bg-blue-500";
    case "Viewer":
      return "bg-gray-500";
  }
};

const filteredUsers = computed(() => {
  return users.value.filter((u) => {
    const matchSearch =
      u.name.toLowerCase().includes(search.value.toLowerCase()) ||
      u.email.toLowerCase().includes(search.value.toLowerCase());

    const matchRole = filterRole.value
      ? u.role === filterRole.value
      : true;

    return matchSearch && matchRole;
  });
});
</script>