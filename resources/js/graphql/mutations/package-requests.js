import gql from 'graphql-tag';

export const APPROVE_PACKAGE_REQUEST = gql`
    mutation ($id: ID!) {
        approvePackageRequest(id: $id) {
            id
            status
        }
    }
`;

export const REJECT_PACKAGE_REQUEST = gql`
    mutation ($id: ID!) {
        rejectPackageRequest(id: $id) {
            id
            status
        }
    }
`;