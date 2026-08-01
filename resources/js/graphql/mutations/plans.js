import gql from 'graphql-tag';

export const CREATE_PLAN = gql`
    mutation CreatePlan(
        $input: CreatePlanInput!
    ) {
        createPlan(input: $input) {
            id
            plan_name
            price

            features {
                id
                name
                group

                benefits {
                    id
                    value
                }
            }
        }
    }
`;

export const UPDATE_PLAN = gql`
    mutation UpdatePlan(
        $id: ID!
        $input: UpdatePlanInput!
    ) {
        updatePlan(
            id: $id
            input: $input
        ) {
            id
            plan_name
            price

            features {
                id
                name
                group

                benefits {
                    id
                    value
                }
            }
        }
    }
`;

export const DELETE_PLAN = gql`
    mutation DeletePlan(
        $id: ID!
    ) {
        deletePlan(id: $id)
    }
`;