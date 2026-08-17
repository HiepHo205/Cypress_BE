import { ref, computed, watch } from 'vue';
import { useQuery } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';

import { GET_CASE_STUDY } from '../../graphql/queries/caseStudy';

import {
    UPDATE_CASE_STUDY,
    DELETE_CASE_STUDY
} from '../../graphql/mutations/caseStudy';

import { apolloClient } from '../../apollo';

export function useCaseStudy(collection = 'caseStudies') {
    const toast = useToast();

    const loading = ref(false);

    const allowedCollections = ['banner', 'categories', 'caseStudies'];

    const {
        result,
        refetch,
        loading: queryLoading
    } = useQuery(GET_CASE_STUDY, null, {
        fetchPolicy: 'network-only'
    });

    const caseStudy = computed(() => {
        return result.value?.caseStudyPage ?? null;
    });

    const getSection = (section) => {
        return computed(() => {
            return caseStudy.value?.[section] ?? [];
        });
    };

    const normalizeCategories = (data) => {
        if (!data) {
            return [];
        }

        if (Array.isArray(data)) {
            return data;
        }

        if (typeof data === 'string') {
            try {
                const parsed = JSON.parse(data);

                if (Array.isArray(parsed)) {
                    return parsed;
                }

                if (parsed && typeof parsed === 'object') {
                    return [parsed];
                }

                return [];
            } catch (error) {
                return [];
            }
        }

        if (typeof data === 'object' && data !== null) {
            return [data];
        }

        return [];
    };

    const categories = computed(() => {
        return normalizeCategories(caseStudy.value?.categories);
    });

    const categoryChildren = computed(() => {
        return categories.value.flatMap((category) => {
            if (!category || !Array.isArray(category.children)) {
                return [];
            }

            return category.children;
        });
    });

    const caseStudies = computed(() => {
        const data = caseStudy.value?.caseStudies ?? [];

        return Array.isArray(data) ? data : [];
    });

    const banner = computed(() => {
        const data = caseStudy.value?.banner ?? null;

        if (!data) {
            return null;
        }

        if (typeof data === 'string') {
            try {
                return JSON.parse(data);
            } catch (error) {
                return null;
            }
        }

        return data;
    });

    const clonePayload = (value) => {
        if (value === undefined || value === null) {
            return value;
        }

        try {
            return JSON.parse(JSON.stringify(value));
        } catch (error) {
            return value;
        }
    };

    const normalizeCategoryPayload = (input) => {
        const source = clonePayload(input);

        if (!source || typeof source !== 'object') {
            return {};
        }

        const children = Array.isArray(source.children) ? source.children : [];

        const normalizedChildren = children
            .map((child) => {
                if (!child || typeof child !== 'object') {
                    return null;
                }

                const name = String(child.name ?? '').trim();

                if (!name) {
                    return null;
                }

                const normalizedChild = {
                    name
                };

                if (child.id && !String(child.id).startsWith('new-')) {
                    normalizedChild.id = String(child.id);
                }

                return normalizedChild;
            })
            .filter(Boolean);

        const payload = {
            ...source,
            title: String(source.title ?? '').trim(),
            children: normalizedChildren
        };

        if (source.id) {
            payload.id = String(source.id);
        }

        return payload;
    };

    const parseResponse = (data) => {
        if (data === undefined || data === null) {
            return data;
        }

        if (typeof data !== 'string') {
            return data;
        }

        try {
            return JSON.parse(data);
        } catch (error) {
            return data;
        }
    };

    const getToastMessages = (type, isNew = false) => {
        if (type === 'categories') {
            return {
                loading: isNew
                    ? 'Creating category...'
                    : 'Updating category...',

                success: isNew
                    ? 'Category created successfully!'
                    : 'Category updated successfully!',

                error: isNew
                    ? 'Create category failed!'
                    : 'Update category failed!'
            };
        }

        if (type === 'banner') {
            return {
                loading: 'Updating case study banner...',

                success: 'Case Study banner updated successfully!',

                error: 'Update Case Study banner failed!'
            };
        }

        return {
            loading: isNew
                ? 'Creating case study...'
                : 'Updating case study...',

            success: isNew
                ? 'Case Study created successfully!'
                : 'Case Study updated successfully!',

            error: isNew
                ? 'Create Case Study failed!'
                : 'Update Case Study failed!'
        };
    };

    const validateCollection = () => {
        if (!allowedCollections.includes(collection)) {
            throw new Error(`Invalid Case Study collection: ${collection}`);
        }
    };

    const updateItem = async (input, image = null, logo = null) => {
        validateCollection();

        if (!input || typeof input !== 'object') {
            throw new Error('Invalid input.');
        }

        if (Object.keys(input).length === 0) {
            throw new Error('Input data is empty.');
        }

        loading.value = true;

        const isNew = !input.id;

        const messages = getToastMessages(collection, isNew);

        const toastId = toast.info(messages.loading, {
            timeout: false
        });

        try {
            let payload;

            if (collection === 'categories') {
                payload = normalizeCategoryPayload(input);
            } else {
                payload = clonePayload(input);
            }

            if (!payload || typeof payload !== 'object') {
                throw new Error('Invalid payload.');
            }

            if (collection === 'categories') {
                if (!Array.isArray(payload.children)) {
                    payload.children = [];
                }

                payload.children = payload.children.map((child) => ({
                    ...child
                }));
            }

            const serializedInput = JSON.stringify(payload);
            const variables = {
                section: collection,

                input: serializedInput,

                action: isNew ? 'create' : 'update'
            };

            if (payload.id) {
                variables.id = String(payload.id);
            }

            if (image) {
                variables.image = image;
            }
            const response = await apolloClient.mutate({
                mutation: UPDATE_CASE_STUDY,

                variables
            });

            const updatedItem = parseResponse(response?.data?.updateCaseStudy);

            await refetch();
            toast.dismiss(toastId);

            toast.success(messages.success);

            return updatedItem;
        } catch (error) {
            toast.dismiss(toastId);

            toast.error(messages.error);

            throw error;
        } finally {
            loading.value = false;
        }
    };

    const removeItem = async (id) => {
        validateCollection();

        if (!id) {
            toast.error('Item ID is not provided.');

            throw new Error('Item ID is not provided.');
        }

        if (collection === 'banner') {
            toast.error('Case Study banner does not support deletion.');

            throw new Error('Case Study banner does not support deletion.');
        }

        loading.value = true;

        const toastId = toast.info(
            collection === 'categories'
                ? 'Deleting category...'
                : 'Deleting case study...',
            {
                timeout: false
            }
        );

        try {
            const response = await apolloClient.mutate({
                mutation: DELETE_CASE_STUDY,

                variables: {
                    section: collection,

                    id: String(id)
                }
            });

            await refetch();

            toast.dismiss(toastId);

            toast.success(
                collection === 'categories'
                    ? 'Category deleted successfully!'
                    : 'Case Study deleted successfully!'
            );

            return parseResponse(response?.data?.deleteCaseStudy);
        } catch (error) {
            toast.dismiss(toastId);

            toast.error(
                collection === 'categories'
                    ? 'Delete category failed!'
                    : 'Delete Case Study failed!'
            );

            throw error;
        } finally {
            loading.value = false;
        }
    };

    const deleteImage = async (id) => {
        if (!id) {
            toast.error('Case Study ID is not provided.');

            throw new Error('Case Study ID is not provided.');
        }

        loading.value = true;

        try {
            const payload = {
                id: String(id),
                image: null
            };

            const variables = {
                section: 'caseStudies',
                input: JSON.stringify(payload),

                action: 'delete-image',

                id: String(id)
            };
            const response = await apolloClient.mutate({
                mutation: UPDATE_CASE_STUDY,

                variables
            });
            await refetch();
            const parsed = parseResponse(response?.data?.updateCaseStudy);
            toast.success('Case Study image deleted successfully!');
            return parsed;
        } catch (error) {
            toast.error('Delete Case Study image failed!');
            throw error;
        } finally {
            loading.value = false;
        }
    };

    return {
        loading,
        queryLoading,
        caseStudy,
        banner,
        categories,
        categoryChildren,
        caseStudies,
        getSection,
        updateItem,
        removeItem,
        deleteImage,
        refetch
    };
}
