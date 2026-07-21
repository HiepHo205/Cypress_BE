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
