import gql from 'graphql-tag';

export const GET_PACKAGE_REQUESTS = gql`
    query {
        packageRequests {
            id
            plan_id
            plan_name
            full_name
            email
            phone
            company_name
            status
        }
    }
`;

export const GET_PACKAGE_REQUEST = gql`
    query ($id: ID!) {
        packageRequest(id: $id) {
            id
            plan_id
            plan_name
            full_name
            email
            phone
            company_name
            request_data
            status
        }
    }
`;
