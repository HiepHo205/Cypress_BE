import gql from 'graphql-tag';

export const UPDATE_LOGO = gql`
    mutation UpdateLogo($logo: Upload!) {
        updateLogo(logo: $logo) {
            logo
        }
    }
`;
