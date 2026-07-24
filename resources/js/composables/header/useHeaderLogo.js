import { ref } from 'vue';
import { useApolloClient } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';
import { UPDATE_LOGO } from '@/graphql/mutations/logo';
import { GET_LOGO } from '@/graphql/queries/logo';

export default function useHeaderLogo() {
    const logo = ref(null);
    const loading = ref(false);
    const toast = useToast();
    const { resolveClient } = useApolloClient();

    const getLogo = async () => {
        loading.value = true;

        try {
            const response = await resolveClient().query({
                query: GET_LOGO,
                fetchPolicy: 'network-only'
            });

            logo.value = response.data?.getLogo?.logo ?? null;

            return logo.value;
        } catch (error) {
            console.error(error);
            return null;
        } finally {
            loading.value = false;
        }
    };

    const updateLogo = async (file) => {
        const toastId = toast.info('Updating logo...', {
            timeout: false
        });

        loading.value = true;

        try {
            const result = await resolveClient().mutate({
                mutation: UPDATE_LOGO,
                variables: {
                    logo: file
                }
            });

            logo.value = result.data.updateLogo.logo;

            toast.update(toastId, {
                content: 'Logo updated successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return logo.value;
        } catch (error) {
            console.error(error);

            toast.update(toastId, {
                content: 'Failed to update logo',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return null;
        } finally {
            loading.value = false;
        }
    };

    return {
        logo,
        loading,
        getLogo,
        updateLogo
    };
}
