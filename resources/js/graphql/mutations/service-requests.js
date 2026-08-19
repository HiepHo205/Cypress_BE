import { gql } from '@apollo/client/core';

export const APPROVE_SERVICE_REQUEST = gql`
    mutation ApproveServiceRequest(
        $id: ID!
    ) {
        approveServiceRequest(
            id: $id
        ) {
            id
            status
        }
    }
`;

export const REJECT_SERVICE_REQUEST = gql`
    mutation RejectServiceRequest(
        $id: ID!
    ) {
        rejectServiceRequest(
            id: $id
        ) {
            id
            status
        }
    }
`;