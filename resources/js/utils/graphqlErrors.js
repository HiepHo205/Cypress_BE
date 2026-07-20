/**
 * Extract human-readable messages from Apollo / GraphQL errors.
 */
export function getGraphQLErrorMessages(error) {
    if (!error) {
        return [];
    }

    const messages = [];

    if (error.graphQLErrors?.length) {
        for (const graphQLError of error.graphQLErrors) {
            const validation = graphQLError.extensions?.validation;

            if (validation) {
                for (const fieldMessages of Object.values(validation)) {
                    messages.push(...fieldMessages);
                }
                continue;
            }

            if (graphQLError.message) {
                messages.push(graphQLError.message);
            }
        }
    }

    if (messages.length === 0 && error.message) {
        messages.push(error.message);
    }

    return messages;
}
