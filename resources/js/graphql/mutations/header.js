import gql from 'graphql-tag';

export const CREATE_HEADER_MENU = gql`
    mutation CreateHeaderMenu($input: HeaderMenuInput!) {
        createHeaderMenu(input: $input) {
            id
            label
            children {
                id
                label
                url
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
                url
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

export const UPDATE_HEADER_COUNTDOWN = gql`
    mutation UpdateHeaderCountdown($input: UpdateHeaderCountdownInput!) {
        updateHeaderCountdown(input: $input) {
            success
            message
        }
    }
`;
export const UPDATE_HEADER_FAVICON = gql`
    mutation UpdateFavicon($favicon: Upload!) {
        updateFavicon(favicon: $favicon) {
            url
            public_id
        }
    }
`;
export const DELETE_HEADER_SUBMENU = gql`
    mutation DeleteHeaderSubMenu($id: ID!) {
        deleteHeaderSubMenu(id: $id) {
            message
        }
    }
`;
