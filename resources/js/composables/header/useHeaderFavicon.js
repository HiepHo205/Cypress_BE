import { ref } from 'vue';
import { useApolloClient } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';

import { GET_HEADER_FAVICON } from '@/graphql/queries/header';
import { UPDATE_HEADER_FAVICON } from '@/graphql/mutations/header';

const favicon = ref(null);
const loaded = ref(false);

export default function useHeaderFavicon() {
    const { resolveClient } = useApolloClient();
    const toast = useToast();

    const getFavicon = async () => {
        if (loaded.value) {
            return favicon.value;
        }

        try {
            const response = await resolveClient().query({
                query: GET_HEADER_FAVICON,
                fetchPolicy: 'network-only'
            });

            favicon.value = response.data.getFavicon ?? null;

            loaded.value = true;

            return favicon.value;
        } catch (error) {
            console.error(error);

            return null;
        }
    };

    const updateFavicon = async (file) => {
        const toastId = toast.info('Updating favicon...', {
            timeout: false
        });

        try {
            const response = await resolveClient().mutate({
                mutation: UPDATE_HEADER_FAVICON,
                variables: {
                    favicon: file
                }
            });

            favicon.value = response.data.updateFavicon ?? null;

            loaded.value = true;

            toast.update(toastId, {
                content: 'Favicon updated successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return favicon.value;
        } catch (error) {
            console.error(error);

            toast.update(toastId, {
                content: 'Failed to update favicon',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return null;
        }
    };

    return {
        favicon,
        getFavicon,
        updateFavicon
    };
}
