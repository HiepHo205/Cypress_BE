import { ref } from 'vue';
import { useApolloClient } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';
import { UPDATE_LOGO } from '@/graphql/mutations/logo';
import { GET_LOGO } from '@/graphql/queries/logo';

const logo = ref(null);

const isUnauthenticated = (error) => {
    const errors =
        error?.graphQLErrors ||
        error?.errors ||
        error?.networkError?.result?.errors ||
        [];

    return errors.some((e) => e.message === 'Unauthenticated.');
};

export default function useHeaderLogo() {
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
            console.error('Get logo error:', error);

            if (isUnauthenticated(error)) {
                return logo.value;
            }

            toast.error('Failed to load logo');

            return logo.value;
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
            const response = await resolveClient().mutate({
                mutation: UPDATE_LOGO,
                variables: {
                    logo: file
                }
            });

            logo.value = response.data.updateLogo.logo;

            toast.update(toastId, {
                content: 'Logo updated successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return logo.value;
        } catch (error) {
            console.error('Update logo error:', error);

            const isUnauthenticated = error?.graphQLErrors?.some(
                (err) => err.message === 'Unauthenticated.'
            );

            if (isUnauthenticated) {
                toast.dismiss(toastId);
                return null;
            }

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
