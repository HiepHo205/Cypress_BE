import { gql } from '@apollo/client/core';

export const DELETE_ROLE = gql`
    mutation DeleteRole($id: ID!) {
        deleteRole(id: $id)
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
