import { useQuery, useMutation } from '@vue/apollo-composable';
import { GET_FOOTER } from '../../graphql/queries/footer';
import { UPDATE_SOCIAL } from '../../graphql/mutations/footer';

export default function useFooterSocial() {
    const { result, refetch } = useQuery(GET_FOOTER);
    const { mutate } = useMutation(UPDATE_SOCIAL);

    const getSocial = async () => {
        await refetch();
        return result.value?.footerSocials ?? [];
    };

    const saveSocials = async (socials) => {
        return await mutate({
            input: {
                socials: socials.map((social) => ({
                    id: social.id,
                    name: social.name,
                    url: social.url,
                    icon: social.icon instanceof File ? social.icon : null
                }))
            }
        });
    };

    return {
        getSocial,
        saveSocials
    };
}
