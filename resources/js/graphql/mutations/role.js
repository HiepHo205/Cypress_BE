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
    mutation UpdateRole($id: ID!, $name: String!, $description: String) {
        updateRole(id: $id, name: $name, description: $description) {
            id
            name
            description
        }
    }
`;

export const CREATE_ROLE = gql`
    mutation CreateRole($name: String!, $description: String) {
        createRole(name: $name, description: $description) {
            id
            name
            description
        }
    }
`;
