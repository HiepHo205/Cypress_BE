import { gql } from '@apollo/client/core';

export const GET_ROLES = gql`
    query GetRoles {
        roles {
            id
            name
            description
            users {
                id
                name
                email
                status
            }
            permissions {
                id
                code
                description
            }
        }
    }
`;

export const UPDATE_ROLE = gql`
    mutation UpdateRole($id: ID!, $name: String!, $description: String) {
        updateRole(id: $id, name: $name, description: $description) {
            id
            name
            description
        }
    }
`;

export const REMOVE_USER_ROLE = gql`
    mutation RemoveUserRole($userId: ID!) {
        removeUserRole(userId: $userId)
    }
`;
