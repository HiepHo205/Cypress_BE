import gql from 'graphql-tag';

export const UPDATE_HOMEPAGE_SECTION = gql`
    mutation UpdateHomepageSection(
        $section: String!
        $input: JSON!
        $image: Upload
        $logo: Upload
    ) {
        updateHomepageSection(
            section: $section
            input: $input
            image: $image
            logo: $logo
        )
    }
`;

export const UPDATE_HOMEPAGE_ITEM = gql`
    mutation UpdateHomepageItem(
        $section: String!
        $field: String!
        $input: JSON!
        $uploadFields: String
        $image: Upload
        $logo: Upload
        $folder: String
    ) {
        updateHomepageItem(
            section: $section
            field: $field
            input: $input
            uploadFields: $uploadFields
            image: $image
            logo: $logo
            folder: $folder
        )
    }
`;

export const DELETE_HOMEPAGE_ITEM = gql`
    mutation DeleteHomepageItem($section: String!, $field: String!, $id: ID!) {
        deleteHomepageItem(section: $section, field: $field, id: $id)
    }
`;
