import { useApolloClient } from '@vue/apollo-composable';

import { GET_FOOTER_NEWSLETTER } from '../../graphql/queries/footer';
import { UPDATE_FOOTER_NEWSLETTER } from '../../graphql/mutations/footer';

const newsletter = {
    id: null,
    title: '',
    description: '',
    email_placeholder: '',
    button_text: ''
};

const isUnauthenticated = (error) => {
    const errors =
        error?.graphQLErrors ||
        error?.errors ||
        error?.networkError?.result?.errors ||
        [];

    return errors.some((e) => e.message === 'Unauthenticated.');
};

export default function useFooterNewsletter() {
    const { resolveClient } = useApolloClient();

    const getNewsletter = async () => {
        try {
            const response = await resolveClient().query({
                query: GET_FOOTER_NEWSLETTER,
                fetchPolicy: 'network-only'
            });

            return response.data?.footerNewsletter ?? newsletter;
        } catch (error) {
            console.error('Get footer newsletter error:', error);

            if (isUnauthenticated(error)) {
                return newsletter;
            }

            return newsletter;
        }
    };

    const updateNewsletter = async (data) => {
        try {
            return await resolveClient().mutate({
                mutation: UPDATE_FOOTER_NEWSLETTER,
                variables: {
                    input: {
                        title: data.title,
                        description: data.description,
                        email_placeholder: data.email_placeholder,
                        button_text: data.button_text
                    }
                }
            });
        } catch (error) {
            console.error('Update footer newsletter error:', error);

            throw error;
        }
    };

    return {
        getNewsletter,
        updateNewsletter
    };
}
