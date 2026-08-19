import gql from 'graphql-tag';

export const GET_LOCATIONS = gql`
    query GetLocations {
        locations {
            id
            name
            address
            latitude
            longitude
            status
        }
    }
`;

export const GET_LOCATION = gql`
    query GetLocation($id: ID!) {
        location(id: $id) {
            id
            name
            address
            latitude
            longitude
            status
        }
    }
`;