import gql from 'graphql-tag';

export const GET_SERVICE_REQUESTS = gql`
    query {
        serviceRequests {
            id
            request_type
            full_name
            email
            phone
            company_name
            status
        }
    }
`;

export const GET_SERVICE_REQUEST = gql`
    query GetServiceRequest(
        $id: ID!
    ) {
        serviceRequest(
            id: $id
        ) {
            id

            request_type

            full_name
            email
            phone

            company_name
            address

            service_interest
            message

            extra_data

            status
        }
    }
`;