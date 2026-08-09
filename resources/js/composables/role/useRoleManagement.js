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

    const getAuthContext = () => {
        const token = localStorage.getItem('token');

        if (!token) {
            return {};
        }

        return {
            context: {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        };
    };

    const isUnauthenticated = (error) => {
        const errors =
            error?.graphQLErrors ||
            error?.errors ||
            error?.networkError?.result?.errors ||
            [];

        return errors.some((err) => err.message === 'Unauthenticated.');
    };

    async function createRole(payload) {
        const toastId = toast.info('Creating role...', {
            timeout: false
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

            toast.update(toastId, {
                content: 'Role created successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return newRole;
        } catch (error) {
            console.error('Create role error:', error);

            if (isUnauthenticated(error)) {
                toast.dismiss(toastId);
                return null;
            }

            toast.update(toastId, {
                content: 'Failed to create role',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return null;
        }
    }

    async function updateRole(payload) {
        const toastId = toast.info('Updating role...', {
            timeout: false
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

            toast.update(toastId, {
                content: 'Role updated successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return data.updateRole;
        } catch (error) {
            console.error('Update role error:', error);

            if (isUnauthenticated(error)) {
                toast.dismiss(toastId);
                return null;
            }

            toast.update(toastId, {
                content: 'Failed to update role',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return null;
        }
    }

    async function deleteRole(role) {
        const toastId = toast.info('Deleting role...', {
            timeout: false
        });

        try {
            const response = await deleteRoleMutation(
                {
                    id: role.id
                },
                getAuthContext()
            );

            roles.value = roles.value.filter((item) => item.id !== role.id);

            toast.update(toastId, {
                content: 'Role deleted successfully',
                options: {
                    type: 'success',
                    timeout: 3000
                }
            });

            return response.data;
        } catch (error) {
            console.error('Delete role error:', error);

            if (isUnauthenticated(error)) {
                toast.dismiss(toastId);
                return null;
            }

            toast.update(toastId, {
                content: 'Failed to delete role',
                options: {
                    type: 'error',
                    timeout: 3000
                }
            });

            return null;
        }
    }

    return {
        createRole,
        updateRole,
        deleteRole
    };
}
