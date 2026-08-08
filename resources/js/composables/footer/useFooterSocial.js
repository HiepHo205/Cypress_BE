import { useApolloClient } from '@vue/apollo-composable';

import { GET_FOOTER } from '../../graphql/queries/footer';
import { UPDATE_SOCIAL } from '../../graphql/mutations/footer';

const socials = [];

const isUnauthenticated = (error) => {
    const errors =
        error?.graphQLErrors ||
        error?.errors ||
        error?.networkError?.result?.errors ||
        [];

    return errors.some((e) => e.message === 'Unauthenticated.');
};

export default function useFooterSocial() {
    const { resolveClient } = useApolloClient();

    const getSocial = async () => {
        try {
            const response = await resolveClient().query({
                query: GET_FOOTER,
                fetchPolicy: 'network-only'
            });

            return response.data?.footerSocials ?? socials;
        } catch (error) {
            console.error('Get footer social error:', error);

            if (isUnauthenticated(error)) {
                return socials;
            }

            return socials;
        }
    };

    const saveSocials = async (socials) => {
        try {
            return await resolveClient().mutate({
                mutation: UPDATE_SOCIAL,
                variables: {
                    input: {
                        socials: socials.map((social) => ({
                            id: social.id,
                            name: social.name,
                            url: social.url,
                            icon:
                                social.icon instanceof File ? social.icon : null
                        }))
                    }
                }
            });
        } catch (error) {
            console.error('Update footer social error:', error);

            throw error;
        }
    };

    return {
        getSocial,
        saveSocials
    };
}
