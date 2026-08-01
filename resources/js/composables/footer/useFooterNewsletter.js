import { watch } from 'vue';
import { useMutation, useQuery } from '@vue/apollo-composable';
import { GET_FOOTER_NEWSLETTER } from '../../graphql/queries/footer';
import { UPDATE_FOOTER_NEWSLETTER } from '../../graphql/mutations/footer';

export default function useFooterNewsletter() {
    const { result, loading } = useQuery(GET_FOOTER_NEWSLETTER);
    const { mutate: updateNewsletterMutation } = useMutation(
        UPDATE_FOOTER_NEWSLETTER
    );

    const getNewsletter = () => {
        return new Promise((resolve) => {
            const stop = watch(
                [result, loading],
                ([value, isLoading]) => {
                    if (isLoading) return;
                    stop();
                    resolve(value?.footerNewsletter ?? null);
                },
                { immediate: true }
            );
        });
    };

    const updateNewsletter = async (data) => {
        return await updateNewsletterMutation({
            input: data
        });
    };

    return {
        getNewsletter,
        updateNewsletter
    };
}
