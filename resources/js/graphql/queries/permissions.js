import gql from 'graphql-tag';

export const GET_PERMISSIONS = gql`
    query GetPermissions {
        permissions {
            id
            code
            description
        }
    }
`;