import gql from 'graphql-tag';

export const UPDATE_NEWS_PAGE = gql`
    mutation UpdateNewsPage(
        $collection: String!
        $input: JSON!
        $image: Upload
    ) {
        updateNewsPage(
            collection: $collection
            input: $input
            image: $image
        )
    }
`;

export const DELETE_NEWS_PAGE_ITEM = gql`
    mutation DeleteNewsPageItem(
        $collection: String!
        $id: ID!
    ) {
        deleteNewsPageItem(
            collection: $collection
            id: $id
        )
    }
`;