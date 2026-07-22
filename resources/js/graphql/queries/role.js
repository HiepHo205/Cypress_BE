import { gql } from '@apollo/client/core';

export const GET_ROLES = gql`
    query GetRoles {
        roles {
            id
            role_name
            description
        }
    }
`;

export const UPDATE_ROLE = gql`
    mutation UpdateRole($id: ID!, $role_name: String!, $description: String) {
        updateRole(id: $id, role_name: $role_name, description: $description) {
            id
            role_name
            description
        }
    }
`;

export const REMOVE_USER_ROLE = gql`
    mutation RemoveUserRole($userId: ID!) {
        removeUserRole(userId: $userId)
    }
`;
