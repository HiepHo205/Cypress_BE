import { useMutation } from '@vue/apollo-composable';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { LOGOUT } from '@/graphql/mutations/auth';

export function useLogout() {
    const router = useRouter();
    const toast = useToast();

    const { mutate: logoutMutation } = useMutation(LOGOUT);

    const logout = async () => {
        try {
            const response = await logoutMutation(
                {},
                {
                    context: {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    }
                }
            );

            toast.success(
                response?.data?.logout?.message || 'Logged out successfully.'
            );
        } catch (err) {
            toast.error(
                err?.graphQLErrors?.[0]?.message ||
                    err.message ||
                    'Logout failed.'
            );
        } finally {
            localStorage.removeItem('token');

            router.push('/login');
        }
    };

    return {
        logout
    };
}
