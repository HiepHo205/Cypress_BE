import { ref } from 'vue';
import { useApolloClient } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';

import { GET_HEADER_COUNTDOWN, GET_HEADER_CTA } from '@/graphql/queries/header';

import { UPDATE_HEADER_COUNTDOWN } from '@/graphql/mutations/header';

const countdown = ref(null);
const loaded = ref(false);

export default function useHeaderCountdown() {
    const { resolveClient } = useApolloClient();
    const toast = useToast();
    const getCountdown = async () => {
        if (loaded.value) {
            return countdown.value;
        }

        try {
            const response = await resolveClient().query({
                query: GET_HEADER_COUNTDOWN,
                fetchPolicy: 'network-only'
            });

            countdown.value = response.data.header.countdown;
            loaded.value = true;

            return countdown.value;
        } catch (error) {
            console.error('Get countdown error:', error);

            toast.error('Failed to load countdown');

            return null;
        }
    };

    const getCta = async () => {
        try {
            const response = await resolveClient().query({
                query: GET_HEADER_CTA,
                fetchPolicy: 'network-only'
            });

            return response.data.header.cta;
        } catch (error) {
            console.error('Get CTA error:', error);

            toast.error('Failed to load CTA');

            return null;
        }
    };
    const updateCountdown = async (data) => {
        const toastId = toast.info('Updating countdown...', {
            timeout: false
        });

        try {
            const response = await resolveClient().mutate({
                mutation: UPDATE_HEADER_COUNTDOWN,

                variables: {
                    input: data
                }
            });

            loaded.value = false;

            await getCountdown();

            toast.update(toastId, {
                content: 'Countdown updated successfully',

                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return response.data;
        } catch (error) {
            console.error('Update countdown error:', error);

            toast.update(toastId, {
                content: 'Failed to update countdown',

                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return null;
        }
    };

    return {
        countdown,

        getCountdown,

        getCta,

        updateCountdown
    };
}
