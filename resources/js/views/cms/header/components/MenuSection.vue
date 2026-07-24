<script setup>
import { ref, onMounted } from "vue";
import { Plus, Pencil, Trash2, Check, X } from "lucide-vue-next";
import ConfirmModal from "@/components/common/ConfirmModal.vue";
import LoadingOverlay from "@/components/common/LoadingOverlay.vue";
import useHeaderMenu from "@/composables/header/useHeaderMenu";

const {
    menus,
    getHeaderMenu,
    createMenu,
    updateMenu: handleUpdateMenu,
    deleteMenu: handleDeleteMenu
} = useHeaderMenu();

const showForm = ref(false);
const editId = ref(null);
const showDeleteModal = ref(false);
const deleteId = ref(null);

const pageLoading = ref(true);

const newMenu = ref({
    label: "",
    children: [
        {
            label: ""
        }
    ]
});

const addMenu = () => {
    showForm.value = true;
    editId.value = null;
};

const addSubMenu = () => {
    newMenu.value.children.push({
        label: ""
    });
};

const removeSubMenu = (index) => {
    newMenu.value.children.splice(index, 1);
};

const resetForm = () => {
    showForm.value = false;
    editId.value = null;

    newMenu.value = {
        label: "",
        children: [
            {
                label: ""
            }
        ]
    };
};

const saveMenu = async () => {
    const success = await createMenu({
        label: newMenu.value.label,
        children: newMenu.value.children.filter(item => item.label)
    });

    if (success) {
        resetForm();
    }
};

const editMenu = (menu) => {
    editId.value = menu.id;

    newMenu.value = {
        label: menu.label,
        children: menu.children.map(item => ({
            label: item.label
        }))
    };
};

const updateMenu = async () => {
    const success = await handleUpdateMenu(
        editId.value,
        {
            label: newMenu.value.label,
            children: newMenu.value.children.filter(item => item.label)
        }
    );

    if (success) {
        resetForm();
    }
};

const openDeleteModal = (id) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    const id = deleteId.value;

    showDeleteModal.value = false;
    deleteId.value = null;

    await handleDeleteMenu(id);
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deleteId.value = null;
};

onMounted(async () => {
    try {
        await getHeaderMenu();
    } finally {
        pageLoading.value = false;
    }
});
</script>

<template>
    <div class="relative rounded-2xl">
        <LoadingOverlay :show="pageLoading" message="Loading menu..." :fullScreen="false" />

        <div :class="pageLoading ? 'pointer-events-none opacity-50' : ''">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Header Menu
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Manage the navigation menu displayed in the website header.
                    </p>
                </div>

                <button @click="addMenu" class="flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-white">
                    <Plus :size="16" />
                    Add Menu
                </button>
            </div>
            <div
                class="mt-8 max-h-[600px] overflow-y-auto rounded-t-2xl border border-gray-100 bg-white scrollbar-hide">
                <table class="min-w-full border-collapse">
                    <thead class="sticky top-0 z-10 bg-gradient-to-r from-[#6C9ADB] to-[#2B71D3]">
                        <tr>
                            <th class="border-r border-gray-100 px-3 py-2 text-center text-lg text-white">
                                Menu
                            </th>

                            <th class="border-r border-gray-100 px-3 py-2 text-center text-lg text-white">
                                Submenus
                            </th>

                            <th class="px-3 py-2 text-center text-lg text-white">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        <tr v-if="showForm && !editId">
                            <td class="border-r border-gray-100 px-3 py-2">
                                <input v-model="newMenu.label" placeholder="Menu name"
                                    class="w-full rounded-lg border border-gray-100 px-3 py-2" />
                            </td>

                            <td class="border-r border-gray-100 px-3 py-2">
                                <div class="space-y-2">
                                    <div v-for="(child, index) in newMenu.children" :key="index"
                                        class="flex items-center gap-2">
                                        <input v-model="child.label" placeholder="Submenu name"
                                            class="flex-1 rounded-lg border border-gray-100 px-3 py-2" />

                                        <button @click="removeSubMenu(index)"
                                            class="rounded bg-red-500 p-1.5 text-white">
                                            <X :size="8" />
                                        </button>
                                    </div>

                                    <div class="flex justify-end">
                                        <button @click="addSubMenu" class="rounded bg-blue-500 p-1.5 text-white">
                                            <Plus :size="8" />
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <td class="px-3 py-2">
                                <div class="flex justify-center gap-2">
                                    <button @click="saveMenu" class="rounded bg-blue-500 p-2 text-white">
                                        <Check :size="15" />
                                    </button>

                                    <button @click="resetForm" class="rounded bg-red-500 p-2 text-white">
                                        <X :size="15" />
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <template v-for="menu in menus" :key="menu.id">
                            <tr v-if="editId === menu.id">
                                <td class="border-r border-gray-100 px-3 py-2">
                                    <input v-model="newMenu.label" placeholder="Menu name"
                                        class="w-full rounded-lg border border-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </td>

                                <td class="border-r border-gray-100 px-3 py-2">
                                    <div class="space-y-2">
                                        <div v-for="(child, index) in newMenu.children" :key="index"
                                            class="flex items-center gap-2">
                                            <input v-model="child.label" placeholder="Submenu name"
                                                class="flex-1 rounded-lg border border-gray-100 px-3 py-2" />

                                            <button @click="removeSubMenu(index)"
                                                class="rounded bg-red-500 p-1.5 text-white">
                                                <X :size="8" />
                                            </button>
                                        </div>

                                        <div class="flex justify-end">
                                            <button @click="addSubMenu" class="rounded bg-blue-500 p-1.5 text-white">
                                                <Plus :size="8" />
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-3 py-2">
                                    <div class="flex justify-center gap-2">
                                        <button @click="updateMenu" class="rounded bg-blue-500 p-2 text-white">
                                            <Check :size="15" />
                                        </button>

                                        <button @click="resetForm" class="rounded bg-red-500 p-2 text-white">
                                            <X :size="15" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-else>
                                <td class="border-r border-gray-100 px-4 py-2 text-center">
                                    {{ menu.label }}
                                </td>

                                <td class="border-r border-gray-100 px-4 py-2 text-center">
                                    <div v-if="menu.children.length" class="space-y-2">
                                        <div v-for="child in menu.children" :key="child.id">
                                            {{ child.label }}
                                        </div>
                                    </div>

                                    <span v-else class="text-gray-400">
                                        —
                                    </span>
                                </td>

                                <td class="px-3 py-2 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button @click="editMenu(menu)" class="rounded bg-blue-500 p-2 text-white">
                                            <Pencil :size="15" />
                                        </button>

                                        <button @click="openDeleteModal(menu.id)"
                                            class="rounded bg-red-500 p-2 text-white">
                                            <Trash2 :size="15" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <ConfirmModal :show="showDeleteModal" title="Delete Menu"
                message="Are you sure you want to delete this menu?" @confirm="confirmDelete" @cancel="cancelDelete" />
        </div>
    </div>
</template>