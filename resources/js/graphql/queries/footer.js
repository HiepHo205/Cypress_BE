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
export const GET_FOOTER = gql`
    query GetFooter {
        footerSocials {
            id
            name
            url
            icon
        }
    }
`;

export const GET_FOOTER_NAVIGATION = gql`
    query GetFooterNavigation {
        footerNavigation {
            id
            navigations {
                id
                group
                title
                link
                type
            }
        }
    }
`;

export const GET_FOOTER_NEWSLETTER = gql`
    query GetFooterNewsletter {
        footerNewsletter {
            id
            title
            description
            placeholder
            button_icon
        }
    }
`;

export const GET_FOOTER_BOTTOM_BAR = gql`
    query GetFooterBottomBar {
        footerBottomBar {
            id
            copyright
            legal_links {
                text
                url
            }
        }
    }
`;
