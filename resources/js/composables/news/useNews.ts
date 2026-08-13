import { ref, computed } from 'vue';
import { useQuery } from '@vue/apollo-composable';
import { useToast } from 'vue-toastification';

import { GET_NEWS_PAGE } from '../../graphql/queries/news';

import {
    UPDATE_NEWS_PAGE,
    DELETE_NEWS_PAGE_ITEM
} from '../../graphql/mutations/news';

import { NewsItemInput } from '../../types/news';
import { apolloClient } from '../../apollo';

type NewsCollection =
    'latest' | 'featured' | 'newsletter' | 'banner' | 'categories';

export function useNews(collection: NewsCollection = 'latest') {
    const toast = useToast();

    const loading = ref(false);

    const { result, refetch } = useQuery(GET_NEWS_PAGE);

    const news = computed(() => {
        const data = result.value?.newsPage ?? null;

        console.log('News page:', data);

        return data;
    });

    const getSection = (section: string) => {
        return computed(() => {
            const data = news.value?.[section] ?? [];

            console.log(`Section [${section}]:`, data);

            return data;
        });
    };

    const categories = computed(() => {
        const data = news.value?.postCategories ?? [];

        if (!Array.isArray(data)) {
            console.log('postCategories is not an array:', data);

            return [];
        }

        return data;
    });

    const categoryOptions = computed(() => {
        const data = categories.value;

        const options = data
            .map((category: any) => {
                if (typeof category === 'string') {
                    return category;
                }

                return (
                    category?.title ??
                    category?.name ??
                    category?.category ??
                    null
                );
            })
            .filter(Boolean);

        const uniqueOptions = [...new Set(options)];

        console.log('Category options:', uniqueOptions);

        return uniqueOptions;
    });

    const parseResponse = (data: unknown) => {
        if (typeof data === 'string') {
            try {
                return JSON.parse(data);
            } catch {
                return data;
            }
        }

        return data;
    };

    const updateItem = async (
        input: NewsItemInput | Record<string, unknown>,
        image: File | null = null
    ) => {
        if (!input || typeof input !== 'object') {
            throw new Error('Invalid input.');
        }

        if (Object.keys(input).length === 0) {
            throw new Error('Input data is empty.');
        }

        loading.value = true;

        const isNew = !('id' in input) || !input.id;

        const toastMessage =
            collection === 'categories'
                ? isNew
                    ? 'Creating category...'
                    : 'Updating category...'
                : collection === 'newsletter'
                  ? 'Updating newsletter...'
                  : collection === 'banner'
                    ? 'Updating banner...'
                    : isNew
                      ? 'Creating news...'
                      : 'Updating news...';

        const toastId = toast.info(toastMessage, {
            timeout: false
        });

        try {
            /**
             * input có thể chứa:
             *
             * title1
             * title2
             * title
             * description
             * inputPlaceholder
             * buttonText
             * buttonUrl
             *
             * hoặc các field khác tùy collection.
             */
            const variables: Record<string, unknown> = {
                collection,
                input: JSON.stringify(input)
            };

            if (image) {
                variables.image = image;
            }

            console.log('Update variables:', variables);

            const response = await apolloClient.mutate({
                mutation: UPDATE_NEWS_PAGE,
                variables
            });

            const updatedItem = parseResponse(response?.data?.updateNewsPage);

            console.log('Updated response:', updatedItem);

            await refetch();

            toast.dismiss(toastId);

            if (collection === 'categories') {
                toast.success(
                    isNew
                        ? 'Category created successfully!'
                        : 'Category updated successfully!'
                );
            } else if (collection === 'newsletter') {
                toast.success('Newsletter updated successfully!');
            } else if (collection === 'banner') {
                toast.success('Banner updated successfully!');
            } else {
                toast.success(
                    isNew
                        ? 'News created successfully!'
                        : 'News updated successfully!'
                );
            }

            return updatedItem;
        } catch (error) {
            toast.dismiss(toastId);

            console.error('Update news error:', error);

            if (collection === 'categories') {
                toast.error(
                    isNew
                        ? 'Create category failed!'
                        : 'Update category failed!'
                );
            } else if (collection === 'newsletter') {
                toast.error('Update newsletter failed!');
            } else if (collection === 'banner') {
                toast.error('Update banner failed!');
            } else {
                toast.error(
                    isNew ? 'Create news failed!' : 'Update news failed!'
                );
            }

            throw error;
        } finally {
            loading.value = false;
        }
    };

    const removeItem = async (id: string) => {
        if (
            collection !== 'latest' &&
            collection !== 'featured' &&
            collection !== 'categories'
        ) {
            toast.error('This section does not support item deletion.');

            return;
        }

        if (!id) {
            toast.error('Item ID is not provided.');

            throw new Error('Item ID is not provided.');
        }

        loading.value = true;

        const toastId = toast.info(
            collection === 'categories'
                ? 'Deleting category...'
                : 'Deleting news...',
            {
                timeout: false
            }
        );

        try {
            console.log('Deleting:', {
                collection,
                id
            });

            const response = await apolloClient.mutate({
                mutation: DELETE_NEWS_PAGE_ITEM,
                variables: {
                    collection,
                    id
                }
            });

            console.log('Delete response:', response?.data);

            await refetch();

            toast.dismiss(toastId);

            toast.success(
                collection === 'categories'
                    ? 'Category deleted successfully!'
                    : 'News deleted successfully!'
            );

            return parseResponse(response?.data?.deleteNewsPageItem);
        } catch (error) {
            toast.dismiss(toastId);

            console.error('Delete news error:', error);

            toast.error(
                collection === 'categories'
                    ? 'Delete category failed!'
                    : 'Delete news failed!'
            );

            throw error;
        } finally {
            loading.value = false;
        }
    };

    return {
        loading,
        news,
        categories,
        categoryOptions,
        getSection,
        updateItem,
        removeItem,
        refetch
    };
}
