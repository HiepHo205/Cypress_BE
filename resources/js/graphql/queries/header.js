import gql from 'graphql-tag';

export const GET_HEADER_MENU = gql`
    query GetHeaderMenu {
        header {
            menus {
                id
                label
                children {
                    id
                    label
                    href
                }
            }
        }
    }
`;
