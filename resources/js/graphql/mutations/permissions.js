import gql from 'graphql-tag';

export const ASSIGN_PERMISSION = gql`
    mutation AssignPermission(
        $userId: ID!
        $permissionId: ID!
    ) {
        assignPermission(
            userId: $userId
            permissionId: $permissionId
        )
    }
`;

export const UNASSIGN_PERMISSION = gql`
    mutation UnassignPermission(
        $userId: ID!
        $permissionId: ID!
    ) {
        unassignPermission(
            userId: $userId
            permissionId: $permissionId
        )
    }
`;