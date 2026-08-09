import { ref } from 'vue';
import { useApolloClient } from '@vue/apollo-composable';

import { GET_FOOTER_BOTTOM_BAR } from '@/graphql/queries/footer';
import { UPDATE_FOOTER_BOTTOM_BAR } from '@/graphql/mutations/footer';

const bottomBar = ref(null);
const loaded = ref(false);

const isUnauthenticated = (error) => {
    const errors =
        error?.graphQLErrors ||
        error?.errors ||
        error?.networkError?.result?.errors ||
        [];

    return errors.some((e) => e.message === 'Unauthenticated.');
};

export default function useFooterBottomBar() {
    const { resolveClient } = useApolloClient();

    const getBottomBar = async () => {
        if (loaded.value) {
            return bottomBar.value;
        }

        try {
            const response = await resolveClient().query({
                query: GET_FOOTER_BOTTOM_BAR,
                fetchPolicy: 'network-only'
            });

            bottomBar.value = response.data?.footerBottomBar ?? null;

            loaded.value = true;

            return bottomBar.value;
        } catch (error) {
            console.error('Get footer bottom bar error:', error);

            if (isUnauthenticated(error)) {
                return bottomBar.value;
            }

            return bottomBar.value;
        }
    };

    const updateBottomBar = async (data) => {
        const toast = useToast();

        const toastId = toast.info('Updating bottom bar...', {
            timeout: false
        });

        try {
            const response = await resolveClient().mutate({
                mutation: UPDATE_FOOTER_BOTTOM_BAR,
                variables: {
                    input: data
                }
            });

            loaded.value = false;

            await getBottomBar();

            toast.update(toastId, {
                content: 'Bottom bar updated successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return response.data;
        } catch (error) {
            console.error('Update footer bottom bar error:', error);

            const isUnauthenticated = error?.graphQLErrors?.some(
                (err) => err.message === 'Unauthenticated.'
            );

            if (isUnauthenticated) {
                toast.dismiss(toastId);
                return null;
            }

            toast.update(toastId, {
                content: 'Failed to update bottom bar',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return null;
        }
    };

    return {
        bottomBar,
        getBottomBar,
        updateBottomBar
    };
}
