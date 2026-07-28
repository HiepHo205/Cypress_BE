import { useQuery, useMutation } from '@vue/apollo-composable';
import { GET_FOOTER_NAVIGATION } from '../../graphql/queries/footer';
import { UPDATE_FOOTER_NAVIGATION } from '../../graphql/mutations/footer';

export default function useFooterNavigation() {
    const { refetch } = useQuery(GET_FOOTER_NAVIGATION);
    const { mutate } = useMutation(UPDATE_FOOTER_NAVIGATION);

    const getNavigation = async () => {
        const response = await refetch();

        return (
            response.data.footerNavigation ?? {
                id: null,
                navigations: []
            }
        );
    };

    const saveNavigation = async (navigations) => {
        return await mutate({
            input: {
                navigations: navigations.map((item) => ({
                    id: item.id,
                    group: item.group,
                    title: item.title,
                    link: item.link,
                    type: item.type ?? 'link'
                }))
            }
        });
    };

    return {
        getNavigation,
        saveNavigation
    };
}
