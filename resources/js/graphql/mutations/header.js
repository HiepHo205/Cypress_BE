import gql from 'graphql-tag';

export const CREATE_HEADER_MENU = gql`
    mutation CreateHeaderMenu($input: HeaderMenuInput!) {
        createHeaderMenu(input: $input) {
            id
            label
            children {
                id
                label
                href
            }
        }
    }
`;

export const UPDATE_HEADER_MENU = gql`
    mutation UpdateHeaderMenu($id: ID!, $input: HeaderMenuInput!) {
        updateHeaderMenu(id: $id, input: $input) {
            id
            label
            children {
                id
                label
                href
            }
        }
    }
`;

export const DELETE_HEADER_MENU = gql`
    mutation DeleteHeaderMenu($id: ID!) {
        deleteHeaderMenu(id: $id) {
            message
        }
    }
`;
