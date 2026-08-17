import { gql } from '@apollo/client/core';

export const UPDATE_CASE_STUDY = gql`
    mutation UpdateCaseStudy(
        $section: String!
        $input: JSON!
        $image: Upload
        $action: String
        $id: ID
    ) {
        updateCaseStudy(
            section: $section
            input: $input
            image: $image
            action: $action
            id: $id
        )
    }
`;

export const DELETE_CASE_STUDY = gql`
    mutation DeleteCaseStudy($section: String!, $id: ID!) {
        deleteCaseStudy(section: $section, id: $id)
    }
`;