import { useMutation } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';

import {
    CREATE_ROLE,
    UPDATE_ROLE,
    DELETE_ROLE
} from '@/graphql/mutations/role';

export function useRoleManagement(roles) {
    const toast = useToast();

    const { mutate: createRoleMutation } = useMutation(CREATE_ROLE);
    const { mutate: updateRoleMutation } = useMutation(UPDATE_ROLE);
    const { mutate: deleteRoleMutation } = useMutation(DELETE_ROLE);

    const getAuthContext = () => ({
        context: {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        }
    });

    async function createRole(payload) {
        const toastId = toast.info('Creating role...', {
            timeout: false,
            closeOnClick: false,
            draggable: false
        });

        try {
            const { data } = await createRoleMutation(
                {
                    role_name: payload.role_name,
                    description: payload.description
                },
                getAuthContext()
            );

            const newRole = data.createRole;

            const adminIndex = roles.value.findIndex(
                (role) => role.role_name?.toLowerCase() === 'admin'
            );

            if (adminIndex !== -1) {
                roles.value.splice(adminIndex + 1, 0, newRole);
            } else {
                roles.value.unshift(newRole);
            }

            toast.dismiss(toastId);
            toast.success('Role created successfully');
        } catch (err) {
            toast.dismiss(toastId);

            toast.error(
                err?.graphQLErrors?.[0]?.message ||
                    err?.networkError?.message ||
                    err.message ||
                    'Create role failed'
            );
        }
    }

    async function updateRole(payload) {
        const toastId = toast.info('Updating role...', {
            timeout: false,
            closeOnClick: false,
            draggable: false
        });

        try {
            const { data } = await updateRoleMutation(
                {
                    id: payload.id,
                    role_name: payload.role_name,
                    description: payload.description
                },
                getAuthContext()
            );

            const index = roles.value.findIndex(
                (role) => role.id === payload.id
            );

            if (index !== -1) {
                roles.value[index] = data.updateRole;
            }

            toast.dismiss(toastId);
            toast.success('Role updated successfully');
        } catch (err) {
            toast.dismiss(toastId);

            toast.error(
                err?.graphQLErrors?.[0]?.message ||
                    err?.networkError?.message ||
                    err.message ||
                    'Update role failed'
            );
        }
    }

    async function deleteRole(role) {
        const toastId = toast.info('Deleting role...', {
            timeout: false,
            closeOnClick: false,
            draggable: false
        });

        try {
            await deleteRoleMutation(
                {
                    id: role.id
                },
                getAuthContext()
            );

            roles.value = roles.value.filter((item) => item.id !== role.id);

            toast.dismiss(toastId);
            toast.success('Role deleted successfully');
        } catch (err) {
            toast.dismiss(toastId);

            toast.error(
                err?.graphQLErrors?.[0]?.message ||
                    err?.networkError?.message ||
                    err.message ||
                    'Delete role failed'
            );
        }
    }

    return {
        createRole,
        updateRole,
        deleteRole
    };
}
