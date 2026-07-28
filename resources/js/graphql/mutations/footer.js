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
export const CREATE_SOCIAL = gql`
    mutation CreateFooterSocial($input: FooterSocialItemInput!) {
        createFooterSocial(input: $input) {
            id
            name
            url
            icon
        }
    }
`;
export const UPDATE_SOCIAL = gql`
    mutation UpdateFooterSocial($input: FooterSocialInput!) {
        updateFooterSocial(input: $input) {
            socials {
                id
                name
                url
                icon
            }
        }
    }
`;
export const UPDATE_FOOTER_NAVIGATION = gql`
    mutation UpdateFooterNavigation($input: FooterNavigationInput!) {
        updateFooterNavigation(input: $input) {
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
export const UPDATE_FOOTER_NEWSLETTER = gql`
    mutation UpdateFooterNewsletter($input: FooterNewsletterInput!) {
        updateFooterNewsletter(input: $input) {
            id
            title
            description
            placeholder
            button_icon
        }
    }
`;

export const UPDATE_FOOTER_BOTTOM_BAR = gql`
    mutation UpdateFooterBottomBar($input: FooterBottomBarInput!) {
        updateFooterBottomBar(input: $input) {
            id
            copyright
            legal_links {
                text
                url
            }
        }
    }
`;
