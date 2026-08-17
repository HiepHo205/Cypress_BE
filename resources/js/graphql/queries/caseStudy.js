import { gql } from '@apollo/client/core';

export const GET_CASE_STUDY = gql`
    query GetCaseStudy {
        caseStudyPage {
            banner
            categories
            caseStudies {
                id
                title
                description
                categories
                active
                image {
                    url
                    public_id
                }
            }
        }
    }
`;
