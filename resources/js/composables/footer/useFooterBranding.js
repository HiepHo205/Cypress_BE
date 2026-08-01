import { computed } from 'vue';
import { useQuery, useMutation } from '@vue/apollo-composable';

import {
    GET_FOOTER_BRANDING,
    GET_FOOTER_LOGO
} from '../../graphql/queries/footer';

import {
    UPDATE_FOOTER_BRANDING,
    UPDATE_FOOTER_LOGO
} from '../../graphql/mutations/footer';

export default function useFooterBranding() {
    const {
        result: brandingResult,
        loading: brandingLoading,
        refetch: refetchBranding
    } = useQuery(GET_FOOTER_BRANDING);

    const {
        result: logoResult,
        loading: logoLoading,
        refetch: refetchLogo
    } = useQuery(GET_FOOTER_LOGO);

    const { mutate: updateBrandingMutation } = useMutation(
        UPDATE_FOOTER_BRANDING
    );

    const { mutate: updateLogoMutation } = useMutation(UPDATE_FOOTER_LOGO);

    const branding = computed(() => {
        return brandingResult.value?.footerBranding ?? null;
    });

    const logo = computed(() => {
        return logoResult.value?.footerLogo ?? null;
    });

    const loading = computed(() => {
        return brandingLoading.value || logoLoading.value;
    });

    const getBranding = async () => {
        const response = await refetchBranding();

        return response?.data?.footerBranding ?? null;
    };

    const getLogo = async () => {
        const response = await refetchLogo();

        return response?.data?.footerLogo ?? null;
    };

    const updateBranding = async (data) => {
        const response = await updateBrandingMutation({
            input: {
                company_name: data.company_name,
                description: data.description
            }
        });

        return response?.data?.updateFooterBranding ?? null;
    };

    const updateLogo = async (file) => {
        const response = await updateLogoMutation({
            logo: file
        });

        return response?.data?.updateFooterLogo ?? null;
    };

    return {
        branding,
        logo,
        loading,
        getBranding,
        getLogo,
        updateBranding,
        updateLogo
    };
}
