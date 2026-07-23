import gql from 'graphql-tag';

export const GET_USERS = gql`
    query GetUsers($page: Int, $name: String) {
        users(page: $page, name: $name) {
            data {
                id
                name
                email
                status

                role {
                    id
                    name
                }

                created_at
            }

            paginatorInfo {
                currentPage
                lastPage
            }
        }
    }
`;

export const GET_USER = gql`
    query GetUser($id: ID!) {
        user(id: $id) {
            id
            name
            email
            status

            role {
                id
                name
            }
        }
    }
`;
