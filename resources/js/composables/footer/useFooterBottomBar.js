import { watch } from 'vue';
import { useMutation, useQuery } from '@vue/apollo-composable';
import { GET_FOOTER_BOTTOM_BAR } from '@/graphql/queries/footer';
import { UPDATE_FOOTER_BOTTOM_BAR } from '@/graphql/mutations/footer';

export default function useFooterBottomBar() {
    const { result, loading } = useQuery(GET_FOOTER_BOTTOM_BAR);

    const { mutate: updateBottomBarMutation } = useMutation(
        UPDATE_FOOTER_BOTTOM_BAR
    );

    const getBottomBar = () => {
        return new Promise((resolve) => {
            const stop = watch(
                [result, loading],
                ([value, isLoading]) => {
                    if (isLoading) return;
                    stop();
                    resolve(value?.footerBottomBar ?? null);
                },
                {
                    immediate: true
                }
            );
        });
    };

    const updateBottomBar = async (data) => {
        return await updateBottomBarMutation({
            input: data
        });
    };

    return {
        getBottomBar,
        updateBottomBar
    };
}
