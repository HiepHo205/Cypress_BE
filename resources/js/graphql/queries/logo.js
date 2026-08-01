import gql from "graphql-tag";

export const GET_LOGO = gql`
    query GetLogo {
        getLogo {
            logo
        }
    }
`;