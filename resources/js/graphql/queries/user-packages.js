import { gql } from '@apollo/client/core';

export const GET_USER_PACKAGES = gql`
    query GetUserPackages {
        userPackages {
            id
            email
            plan_name
            duration_days
            started_at
            expired_at
            status
        }
    }
`;

export const GET_USER_PACKAGE = gql`
    query GetUserPackage($id: ID!) {
        userPackage(id: $id) {
            id
            email
            plan_name
            duration_days
            started_at
            expired_at
            status
        }
    }
`;