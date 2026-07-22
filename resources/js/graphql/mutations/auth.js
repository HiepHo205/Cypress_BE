import { gql } from '@apollo/client/core';

export const LOGOUT = gql`
    mutation Logout {
        logout {
            status
            message
        }
    }
`;
