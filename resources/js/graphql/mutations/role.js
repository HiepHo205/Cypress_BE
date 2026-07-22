import { gql } from '@apollo/client/core';

export const DELETE_ROLE = gql`
    mutation DeleteRole($id: ID!) {
        deleteRole(id: $id)
    }
`;

export const CHANGE_USER_ROLE = gql`
    mutation ChangeUserRole($userId: ID!, $role: String!) {
        changeUserRole(userId: $userId, role: $role)
    }
`;

export const UPDATE_ROLE = gql`
    mutation UpdateRole($id: ID!, $role_name: String!, $description: String) {
        updateRole(id: $id, role_name: $role_name, description: $description) {
            id
            role_name
            description
            created_at
            updated_at
        }
    }
`;

export const CREATE_ROLE = gql`
    mutation CreateRole($role_name: String!, $description: String) {
        createRole(role_name: $role_name, description: $description) {
            id
            role_name
            description
            created_at
            updated_at
        }
    }
`;
