import gql from 'graphql-tag';

export const CREATE_USER = gql`
    mutation CreateUser($input: CreateUserInput!) {
        createUser(input: $input) {
            user {
                id
                name
                email
                role_id
            }
        }
    }
`;

export const UPDATE_USER = gql`
    mutation UpdateUser($id: ID!, $input: UpdateUserInput!) {
        updateUser(id: $id, input: $input) {
            user {
                id
                name
                email
                status
                role_id
            }
        }
    }
`;

export const DELETE_USER = gql`
    mutation DeleteUser($id: ID!) {
        deleteUser(id: $id) {
            user {
                id
            }
        }
    }
`;

export const DEACTIVATE_USER = gql`
    mutation DeactivateUser($id: ID!) {
        deactivateUser(id: $id) {
            user {
                id
                status
            }
        }
    }
`;
