import { ref } from 'vue';
import { useApolloClient } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';

import { GET_HEADER_MENU } from '@/graphql/queries/header';
import {
    CREATE_HEADER_MENU,
    UPDATE_HEADER_MENU,
    DELETE_HEADER_MENU
} from '@/graphql/mutations/header';

export default function useHeaderMenu() {
    const menus = ref([]);
    const { resolveClient } = useApolloClient();
    const toast = useToast();
    const getHeaderMenu = async () => {
        try {
            const response = await resolveClient().query({
                query: GET_HEADER_MENU,
                fetchPolicy: 'network-only'
            });
            menus.value = [...response.data.header.menus];
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
            toast.dismiss(toastId);
            toast.success('Created menu successfully');
            return true;
        } catch (error) {
            toast.dismiss(toastId);
            console.error('CREATE MENU ERROR:', error);
            toast.error('Create menu failed');
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
            await getHeaderMenu();
            toast.dismiss(toastId);
            toast.success('Updated menu successfully');
            return true;
        } catch (error) {
            toast.dismiss(toastId);
            console.error(error);
            toast.error('Update menu failed');
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
            toast.dismiss(toastId);
            toast.success('Deleted menu successfully');
            return true;
        } catch (error) {
            toast.dismiss(toastId);
            console.error('DELETE MENU ERROR:', error);
            toast.error('Delete menu failed');
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
