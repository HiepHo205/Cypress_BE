import gql from 'graphql-tag';

export const UPDATE_FOOTER_BRANDING = gql`
    mutation UpdateFooterBranding($input: FooterBrandingInput!) {
        updateFooterBranding(input: $input) {
            company_name
            description
        }
    }
`;

export const UPDATE_FOOTER_LOGO = gql`
    mutation UpdateFooterLogo($logo: Upload!) {
        updateFooterLogo(logo: $logo) {
            logo
        }
    }
`;
