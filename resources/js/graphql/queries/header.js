import gql from 'graphql-tag';

export const GET_HEADER_MENU = gql`
    query GetHeaderMenu {
        header {
            menus {
                id
                label
                children {
                    id
                    label
                    url
                }
            }
        }
    }
`;

export const GET_HEADER_COUNTDOWN = gql`
    query GetHeaderCountdown {
        header {
            countdown {
                enabled
                target_date
                button {
                    label
                    href
                }
            }
        }
    }
`;

export const GET_HEADER_FAVICON = gql`
    query GetHeaderFavicon {
        getFavicon {
            url
            public_id
        }
    }
`;

export const GET_HEADER_CTA = gql`
    query GetHeaderCta {
        header {
            countdown {
                button {
                    label
                    href
                }
            }
        }
    }
`;
