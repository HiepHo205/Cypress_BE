import { ref, computed } from 'vue';
import { useMutation, useQuery } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';

import { GET_HOMEPAGE } from '../../graphql/queries/home';

import {
    UPDATE_HOMEPAGE_SECTION,
    UPDATE_HOMEPAGE_ITEM,
    DELETE_HOMEPAGE_ITEM
} from '../../graphql/mutations/home';

export function useHomepage() {
    const toast = useToast();

    const loading = ref(false);

    const { result, refetch } = useQuery(GET_HOMEPAGE);

    const { mutate: updateSection } = useMutation(UPDATE_HOMEPAGE_SECTION);

    const { mutate: updateItem } = useMutation(UPDATE_HOMEPAGE_ITEM);

    const { mutate: deleteItem } = useMutation(DELETE_HOMEPAGE_ITEM);

    const homepage = computed(() => {
        return result.value?.homepage ?? null;
    });

    const getSection = (section: string) => {
        return computed(() => {
            return homepage.value?.[section] ?? null;
        });
    };

    const saveSection = async (
        section: string,
        input: any,
        image: File | null = null,
        logo: File | null = null,
        action: 'create' | 'update' | 'delete' = 'update'
    ) => {
        loading.value = true;

        const message =
            action === 'create'
                ? 'Creating new data...'
                : action === 'delete'
                  ? 'Deleting data...'
                  : 'Updating data...';

        const successMessage =
            action === 'create'
                ? 'Created successfully!'
                : action === 'delete'
                  ? 'Deleted successfully!'
                  : 'Updated successfully!';

        const toastId = toast.info(message, {
            timeout: false
        });

        try {
            const response = await updateSection({
                section,
                input: JSON.stringify(input),
                image,
                logo
            });

            await refetch();

            toast.dismiss(toastId);

            toast.success(successMessage);

            const data = response?.data?.updateHomepageSection;

            return typeof data === 'string' ? JSON.parse(data) : data;
        } catch (error) {
            toast.dismiss(toastId);

            toast.error(
                action === 'create'
                    ? 'Create failed!'
                    : action === 'delete'
                      ? 'Delete failed!'
                      : 'Update failed!'
            );

            throw error;
        } finally {
            loading.value = false;
        }
    };

    const saveItem = async (
        section: string,
        field: string,
        input: any,
        uploadFields: any = null,
        folder: string | null = null
    ) => {
        const toastId = toast.info('Updating data...', {
            timeout: false
        });

        try {
            const image =
                uploadFields?.image instanceof File ? uploadFields.image : null;

            const logo =
                uploadFields?.logo instanceof File ? uploadFields.logo : null;

            const response = await updateItem({
                section,
                field,
                input: JSON.stringify(input),

                uploadFields: JSON.stringify({
                    image: !!image,
                    logo: !!logo
                }),

                folder,

                image,
                logo
            });

            toast.dismiss(toastId);

            toast.success('Updated successfully!');

            const data = response?.data?.updateHomepageItem;

            return typeof data === 'string' ? JSON.parse(data) : data;
        } catch (error) {
            toast.dismiss(toastId);

            toast.error('Update failed!');

            throw error;
        }
    };

    const removeItem = async (section: string, field: string, id: string) => {
        const toastId = toast.info('Deleting data...', {
            timeout: false
        });

        try {
            await deleteItem({
                section,
                field,
                id
            });

            await refetch();

            toast.dismiss(toastId);

            toast.success('Deleted successfully!');
        } catch (error) {
            toast.dismiss(toastId);

            toast.error('Delete failed!');

            throw error;
        }
    };

    return {
        loading,
        homepage,
        getSection,
        saveSection,
        saveItem,
        removeItem,
        refetch
    };
}
