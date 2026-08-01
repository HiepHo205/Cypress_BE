import gql from 'graphql-tag';

export const GET_PLANS = gql`
    query {
        plans {
            id
            plan_name
            price
        }
    }
`;

export const GET_PLAN = gql`
    query GetPlan(
        $id: ID!
    ) {
        plan(id: $id) {
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

export const GET_FEATURES = gql`
    query {
        features {
            id
            name
            group
        }
    }
`;