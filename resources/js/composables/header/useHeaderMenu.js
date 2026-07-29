import { ref } from 'vue';
import { useApolloClient, useMutation } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';

import { GET_HEADER_MENU } from '@/graphql/queries/header';
import {
    CREATE_HEADER_MENU,
    UPDATE_HEADER_MENU,
    DELETE_HEADER_MENU
} from '@/graphql/mutations/header';

const menus = ref([]);
const loaded = ref(false);

export default function useHeaderMenu() {
    const { resolveClient } = useApolloClient();
    const toast = useToast();

    const { mutate: createMenuMutation } = useMutation(CREATE_HEADER_MENU);
    const { mutate: updateMenuMutation } = useMutation(UPDATE_HEADER_MENU);
    const { mutate: deleteMenuMutation } = useMutation(DELETE_HEADER_MENU);

    const getHeaderMenu = async () => {
        if (loaded.value) {
            return;
        }

        try {
            const response = await resolveClient().query({
                query: GET_HEADER_MENU,
                fetchPolicy: 'network-only'
            });

            menus.value = [...response.data.header.menus];
            loaded.value = true;
        } catch (error) {
            console.error(error);
            toast.error('Failed to load header menu');
        }
    };

    const createMenu = async (data) => {
        const toastId = toast.info('Creating menu...', {
            timeout: false
        });

        try {
            const response = await createMenuMutation({
                input: data
            });

            const newMenu = response?.data?.createHeaderMenu;

            if (!newMenu) {
                throw new Error('Create menu response empty');
            }

            menus.value = [...menus.value, newMenu];

            toast.update(toastId, {
                content: 'Menu created successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return true;
        } catch (error) {
            console.error(error);

            toast.update(toastId, {
                content: 'Failed to create menu',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return false;
        }
    };

    const updateMenu = async (id, data, message = 'Updating menu...') => {
        const toastId = toast.info(message, {
            timeout: false
        });

        try {
            await updateMenuMutation({
                id,
                input: data
            });

            loaded.value = false;
            await getHeaderMenu();

            toast.update(toastId, {
                content: message.includes('Deleting')
                    ? 'Submenu deleted successfully'
                    : 'Menu updated successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return true;
        } catch (error) {
            console.error(error);

            toast.update(toastId, {
                content: message.includes('Deleting')
                    ? 'Failed to delete submenu'
                    : 'Failed to update menu',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return false;
        }
    };

    const deleteSubMenu = async (menuId, submenuId) => {
        const toastId = toast.info('Deleting submenu...', {
            timeout: false
        });

        try {
            const menu = menus.value.find((item) => item.id === menuId);

            if (!menu) {
                throw new Error('Menu not found');
            }

            const children = menu.children.filter(
                (child) => child.id !== submenuId
            );

            await updateMenuMutation({
                id: menuId,
                input: {
                    label: menu.label,
                    children
                }
            });

            loaded.value = false;
            await getHeaderMenu();

            toast.update(toastId, {
                content: 'Submenu deleted successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return true;
        } catch (error) {
            console.error(error);

            toast.update(toastId, {
                content: 'Failed to delete submenu',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return false;
        }
    };

    const deleteMenu = async (id) => {
        const toastId = toast.info('Deleting menu...', {
            timeout: false
        });

        try {
            await deleteMenuMutation({
                id
            });

            menus.value = menus.value.filter((item) => item.id !== id);

            toast.update(toastId, {
                content: 'Menu deleted successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return true;
        } catch (error) {
            console.error(error);

            toast.update(toastId, {
                content: 'Failed to delete menu',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return false;
        }
    };

    return {
        menus,
        getHeaderMenu,
        createMenu,
        updateMenu,
        deleteSubMenu,
        deleteMenu
    };
}
