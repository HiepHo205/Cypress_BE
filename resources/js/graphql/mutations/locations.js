import gql from 'graphql-tag';

export const CREATE_LOCATION = gql`
    mutation CreateLocation(
        $input: CreateLocationInput!
    ) {
        createLocation(
            input: $input
        ) {
            id
            name
        }
    }
`;

export const UPDATE_LOCATION = gql`
    mutation UpdateLocation(
        $id: ID!
        $input: UpdateLocationInput!
    ) {
        updateLocation(
            id: $id
            input: $input
        ) {
            id
            name
        }
    }
`;

export const DELETE_LOCATION = gql`
    mutation DeleteLocation(
        $id: ID!
    ) {
        deleteLocation(
            id: $id
        )
    }
`;