import gql from 'graphql-tag';

export const GET_FOOTER_BRANDING = gql`
    query GetFooterBranding {
        footerBranding {
            logo

            company_name

            description
        }
    }
`;
export const GET_FOOTER_LOGO = gql`
    query FooterLogo {
        footerBranding {
            logo
        }
    }
`;
