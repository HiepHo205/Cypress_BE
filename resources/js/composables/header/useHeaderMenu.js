import { ref } from 'vue';
import { useApolloClient } from '@vue/apollo-composable';
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
            const response = await resolveClient().mutate({
                mutation: CREATE_HEADER_MENU,
                variables: {
                    input: data
                }
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

    const updateMenu = async (id, data) => {
        const toastId = toast.info('Updating menu...', {
            timeout: false
        });

        try {
            await resolveClient().mutate({
                mutation: UPDATE_HEADER_MENU,
                variables: {
                    id,
                    input: data
                }
            });

            loaded.value = false;

            await getHeaderMenu();

            toast.update(toastId, {
                content: 'Menu updated successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return true;
        } catch (error) {
            console.error(error);

            toast.update(toastId, {
                content: 'Failed to update menu',
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
            await resolveClient().mutate({
                mutation: DELETE_HEADER_MENU,
                variables: {
                    id
                }
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
        deleteMenu
    };
}
