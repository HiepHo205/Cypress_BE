import { ref, computed } from 'vue';
import { useApolloClient } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';

import {
    GET_FOOTER_BRANDING,
    GET_FOOTER_LOGO
} from '../../graphql/queries/footer';

import {
    UPDATE_FOOTER_BRANDING,
    UPDATE_FOOTER_LOGO
} from '../../graphql/mutations/footer';

const branding = ref(null);
const logo = ref(null);

const isUnauthenticated = (error) => {
    return error?.graphQLErrors?.some(
        (err) => err.message === 'Unauthenticated.'
    );
};

export default function useFooterBranding() {
    const { resolveClient } = useApolloClient();
    const toast = useToast();

    const loading = ref(false);

    const getBranding = async () => {
        try {
            const response = await resolveClient().query({
                query: GET_FOOTER_BRANDING,
                fetchPolicy: 'network-only'
            });

            branding.value = response.data?.footerBranding ?? null;

            return branding.value;
        } catch (error) {
            console.error('Get footer branding error:', error);

            if (isUnauthenticated(error)) {
                return branding.value;
            }

            return branding.value;
        }
    };

    const getLogo = async () => {
        try {
            const response = await resolveClient().query({
                query: GET_FOOTER_LOGO,
                fetchPolicy: 'network-only'
            });

            logo.value = response.data?.footerLogo ?? null;

            return logo.value;
        } catch (error) {
            console.error('Get footer logo error:', error);

            if (isUnauthenticated(error)) {
                return logo.value;
            }

            return logo.value;
        }
    };

    const updateBranding = async (data) => {
        const toastId = toast.info('Updating branding...', {
            timeout: false
        });

        try {
            const response = await resolveClient().mutate({
                mutation: UPDATE_FOOTER_BRANDING,
                variables: {
                    input: {
                        company_name: data.company_name,
                        description: data.description
                    }
                }
            });

            branding.value = response.data?.updateFooterBranding ?? null;

            toast.update(toastId, {
                content: 'Branding updated successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return branding.value;
        } catch (error) {
            console.error('Update footer branding error:', error);

            if (isUnauthenticated(error)) {
                toast.dismiss(toastId);
                return null;
            }

            toast.update(toastId, {
                content: 'Failed to update branding',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return null;
        }
    };

    const updateLogo = async (file) => {
        const toastId = toast.info('Updating footer logo...', {
            timeout: false
        });

        try {
            const response = await resolveClient().mutate({
                mutation: UPDATE_FOOTER_LOGO,
                variables: {
                    logo: file
                }
            });

            logo.value = response.data?.updateFooterLogo ?? null;

            toast.update(toastId, {
                content: 'Footer logo updated successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return logo.value;
        } catch (error) {
            console.error('Update footer logo error:', error);

            if (isUnauthenticated(error)) {
                toast.dismiss(toastId);
                return null;
            }

            toast.update(toastId, {
                content: 'Failed to update footer logo',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return null;
        }
    };

    return {
        branding: computed(() => branding.value),
        logo: computed(() => logo.value),
        loading,
        getBranding,
        getLogo,
        updateBranding,
        updateLogo
    };
}
