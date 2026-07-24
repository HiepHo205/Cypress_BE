import { ref } from 'vue';
import { useQuery, useMutation } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';
import { UPDATE_LOGO } from '@/graphql/mutations/logo';
import { GET_LOGO } from '@/graphql/queries/logo';
export default function useHeaderLogo() {
    const logo = ref(null);
    const loading = ref(false);
    const toast = useToast();
    const { onResult } = useQuery(GET_LOGO);
    onResult((result) => {
        if (result.data?.getLogo?.logo) {
            logo.value = result.data.getLogo.logo;
        }
    });
    const { mutate: updateLogoMutation } = useMutation(UPDATE_LOGO, {
        update(cache, { data }) {
            cache.writeQuery({
                query: GET_LOGO,
                data: {
                    getLogo: {
                        logo: data.updateLogo.logo
                    }
                }
            });
        }
    });
    const updateLogo = async (file) => {
        const toastId = toast.info('Updating logo...', {
            timeout: false
        });
        loading.value = true;
        try {
            const result = await updateLogoMutation({
                logo: file
            });
            toast.dismiss(toastId);
            toast.success('Logo updated successfully');
            return result.data.updateLogo.logo;
        } catch (error) {
            toast.dismiss(toastId);
            console.error(error);
            toast.error('Failed to update logo');
            return null;
        } finally {
            loading.value = false;
        }
    };
    return {
        logo,
        loading,
        updateLogo
    };
}
