import { useApolloClient } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';

import { GET_FOOTER_NAVIGATION } from '../../graphql/queries/footer';
import { UPDATE_FOOTER_NAVIGATION } from '../../graphql/mutations/footer';

const navigation = {
    id: null,
    navigations: []
};

const isUnauthenticated = (error) => {
    return error?.graphQLErrors?.some(
        (err) => err.message === 'Unauthenticated.'
    );
};

export default function useFooterNavigation() {
    const { resolveClient } = useApolloClient();
    const toast = useToast();

    const getNavigation = async () => {
        try {
            const response = await resolveClient().query({
                query: GET_FOOTER_NAVIGATION,
                fetchPolicy: 'network-only'
            });

            return (
                response.data?.footerNavigation ?? {
                    id: null,
                    navigations: []
                }
            );
        } catch (error) {
            console.error('Get footer navigation error:', error);

            if (isUnauthenticated(error)) {
                return navigation;
            }

            return navigation;
        }
    };

    const saveNavigation = async (navigations) => {
        const toastId = toast.info('Updating navigation...', {
            timeout: false
        });

        try {
            const response = await resolveClient().mutate({
                mutation: UPDATE_FOOTER_NAVIGATION,
                variables: {
                    input: {
                        navigations: navigations.map((item) => ({
                            id: item.id,
                            group: item.group,
                            title: item.title,
                            link: item.link,
                            type: item.type ?? 'link'
                        }))
                    }
                }
            });

            toast.update(toastId, {
                content: 'Navigation updated successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return response.data;
        } catch (error) {
            console.error('Update footer navigation error:', error);

            const isUnauthenticatedError = error?.graphQLErrors?.some(
                (err) => err.message === 'Unauthenticated.'
            );

            if (isUnauthenticatedError) {
                toast.dismiss(toastId);
                return null;
            }

            toast.update(toastId, {
                content: 'Failed to update navigation',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return null;
        }
    };

    return {
        getNavigation,
        saveNavigation
    };
}
