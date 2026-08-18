import { ref, computed } from 'vue';
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
    const allowedCollections = [
        'banner',
        'categories',
        'caseStudies',
        'case-study-detail'
    ];
    const {
        result,
        refetch,
        loading: queryLoading
    } = useQuery(GET_CASE_STUDY, null, {
        fetchPolicy: 'network-only'
    });
    const caseStudy = computed(() => {
        const data = result.value?.caseStudyPage ?? null;
        console.log('========== CASE STUDY ==========');
        console.log('CASE STUDY RESULT:', result.value);
        console.log('CASE STUDY PAGE:', data);
        console.log('CASE STUDY DETAIL RAW:', data?.caseStudyDetail);
        console.log('CASE STUDY DETAIL TYPE:', typeof data?.caseStudyDetail);
        console.log('================================');
        return data;
    });
    const parseResponse = (data) => {
        if (data === undefined || data === null) {
            return data;
        }
        if (typeof data !== 'string') {
            return data;
        }
        try {
            return JSON.parse(data);
        } catch {
            return data;
        }
    };
    const normalizeCategories = (data) => {
        const parsed = parseResponse(data);
        if (!parsed) {
            return [];
        }
        if (Array.isArray(parsed)) {
            return parsed;
        }
        if (typeof parsed === 'object') {
            return [parsed];
        }
        return [];
    };
    const normalizeBanner = (data) => {
        const parsed = parseResponse(data);
        if (!parsed || typeof parsed !== 'object') {
            return null;
        }
        return parsed;
    };
    const normalizeCaseStudyDetail = (data) => {
        const parsed = parseResponse(data);
        if (!parsed || typeof parsed !== 'object') {
            return {
                social_media: []
            };
        }
        const socialMedia = Array.isArray(parsed.social_media)
            ? parsed.social_media
            : [];
        return {
            ...(parsed.id
                ? {
                      id: String(parsed.id)
                  }
                : {}),
            social_media: socialMedia
                .filter((social) => social && typeof social === 'object')
                .map((social) => {
                    const icon =
                        social.icon && typeof social.icon === 'object'
                            ? social.icon
                            : {};
                    return {
                        name: String(social.name ?? '').trim(),
                        icon: {
                            url: String(icon.url ?? ''),
                            public_id: String(icon.public_id ?? '')
                        },
                        url: String(social.url ?? '').trim()
                    };
                })
                .filter(
                    (social) =>
                        social.name !== '' ||
                        social.url !== '' ||
                        social.icon.url !== ''
                )
        };
    };
    const banner = computed(() => {
        return normalizeBanner(caseStudy.value?.banner);
    });
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
    const normalizeImage = (image) => {
        if (!image) {
            return null;
        }
        if (typeof image === 'string') {
            return image;
        }
        if (typeof image === 'object') {
            return {
                url: String(image.url ?? ''),
                public_id: String(image.public_id ?? '')
            };
        }
        return null;
    };
    const normalizeTableOfContents = (value) => {
        let data = value ?? [];
        if (typeof data === 'string') {
            data = parseResponse(data);
        }
        if (!Array.isArray(data)) {
            return [];
        }
        return data.map((item, index) => ({
            id: item?.id ?? `toc-${Date.now()}-${index}`,
            order: Number(item?.order) || index + 1,
            title: String(item?.title ?? '').trim(),
            children: Array.isArray(item?.children)
                ? item.children
                      .map((child) => {
                          if (child && typeof child === 'object') {
                              return String(
                                  child.title ?? child.name ?? ''
                              ).trim();
                          }
                          return String(child ?? '').trim();
                      })
                      .filter(Boolean)
                : []
        }));
    };
    const normalizeSections = (value) => {
        let data = value ?? [];
        if (typeof data === 'string') {
            data = parseResponse(data);
        }
        if (!Array.isArray(data)) {
            return [];
        }
        return data.map((section, index) => ({
            id: section?.id ?? `section-${Date.now()}-${index}`,
            type: section?.type ?? 'text',
            title: String(section?.title ?? section?.heading ?? '').trim(),
            content: String(
                section?.content ?? section?.description ?? ''
            ).trim(),
            image: normalizeImage(section?.image),
            imageFile: null
        }));
    };
    const normalizeCaseStudyItem = (item) => {
        if (!item || typeof item !== 'object') {
            return null;
        }
        return {
            ...item,
            id: String(item.id ?? ''),
            title: String(item.title ?? ''),
            description: String(item.description ?? ''),
            categories: Array.isArray(item.categories) ? item.categories : [],
            active: item.active ?? true,
            date: item.date ?? '',
            author: item.author ?? '',
            seriesTags: Array.isArray(item.seriesTags) ? item.seriesTags : [],
            tableOfContents: normalizeTableOfContents(item.tableOfContents),
            sections: normalizeSections(item.sections),
            image: normalizeImage(item.image),
            logo: normalizeImage(item.logo)
        };
    };
    const caseStudies = computed(() => {
        const data = caseStudy.value?.caseStudies ?? [];
        if (!Array.isArray(data)) {
            return [];
        }
        return data.map(normalizeCaseStudyItem).filter(Boolean);
    });
    const caseStudyDetail = computed(() => {
        const raw = caseStudy.value?.caseStudyDetail ?? null;
        console.log('CASE STUDY DETAIL RAW:', raw);
        console.log('CASE STUDY DETAIL RAW TYPE:', typeof raw);
        if (!raw) {
            console.log('CASE STUDY DETAIL EMPTY');
            return {
                social_media: []
            };
        }
        if (typeof raw === 'string') {
            try {
                const parsed = JSON.parse(raw);
                console.log('CASE STUDY DETAIL PARSED:', parsed);
                console.log(
                    'CASE STUDY DETAIL SOCIAL MEDIA:',
                    parsed?.social_media
                );
                return parsed && typeof parsed === 'object'
                    ? parsed
                    : {
                          social_media: []
                      };
            } catch (error) {
                console.error('CASE STUDY DETAIL PARSE ERROR:', error);
                return {
                    social_media: []
                };
            }
        }
        console.log('CASE STUDY DETAIL OBJECT:', raw);
        console.log('CASE STUDY DETAIL SOCIAL MEDIA:', raw?.social_media);
        return raw;
    });
    const getSection = (section) => {
        return computed(() => {
            if (!caseStudy.value) {
                return null;
            }
            if (section === 'banner') {
                return banner.value;
            }
            if (section === 'categories') {
                return categories.value;
            }
            if (section === 'case-study-detail') {
                return caseStudyDetail.value;
            }
            if (section === 'caseStudies') {
                return caseStudies.value;
            }
            return caseStudy.value[section] ?? [];
        });
    };
    const clonePayload = (value) => {
        if (value === undefined || value === null) {
            return value;
        }
        try {
            return JSON.parse(JSON.stringify(value));
        } catch {
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
    const normalizeCaseStudyDetailPayload = (input) => {
        const source = clonePayload(input);
        if (!source || typeof source !== 'object') {
            return {};
        }
        const normalizeImage = (image) => {
            if (!image) {
                return null;
            }
            if (typeof image === 'string') {
                return image;
            }
            if (typeof image === 'object') {
                const result = {
                    url: String(image.url ?? ''),
                    public_id: String(image.public_id ?? '')
                };
                if (
                    image._image_index !== undefined &&
                    image._image_index !== null &&
                    !Number.isNaN(Number(image._image_index))
                ) {
                    result._image_index = Number(image._image_index);
                }
                return result;
            }
            return null;
        };
        const normalizeSocialMedia = (socialMedia) => {
            if (!Array.isArray(socialMedia)) {
                return [];
            }
            return socialMedia
                .filter((social) => social && typeof social === 'object')
                .map((social) => {
                    const result = {
                        name: String(social.name ?? '').trim(),
                        url: String(social.url ?? '').trim(),
                        icon: normalizeImage(social.icon)
                    };
                    if (
                        social._image_index !== undefined &&
                        social._image_index !== null &&
                        !Number.isNaN(Number(social._image_index))
                    ) {
                        result._image_index = Number(social._image_index);
                    }
                    if (social.id) {
                        result.id = String(social.id);
                    }
                    if (social.removeIcon) {
                        result.removeIcon = true;
                    }
                    return result;
                })
                .filter(
                    (social) =>
                        social.name ||
                        social.url ||
                        social.icon?.url ||
                        social._image_index !== undefined
                );
        };
        const normalizeTableOfContents = (toc) => {
            if (!Array.isArray(toc)) {
                return [];
            }
            return toc.map((item, index) => ({
                id: item?.id ?? `toc-${Date.now()}-${index}`,
                order: Number(item?.order) || index + 1,
                title: String(item?.title ?? '').trim(),
                children: Array.isArray(item?.children)
                    ? item.children
                          .map((child) => String(child ?? '').trim())
                          .filter(Boolean)
                    : []
            }));
        };
        const normalizeSections = (sections) => {
            if (!Array.isArray(sections)) {
                return [];
            }
            return sections.map((section, index) => {
                const result = {
                    id: section?.id ?? `section-${Date.now()}-${index}`,
                    type: section?.type ?? 'text',
                    title: String(section?.title ?? '').trim(),
                    content: String(section?.content ?? '').trim(),
                    image: normalizeImage(section?.image)
                };
                if (
                    section?._image_index !== undefined &&
                    section?._image_index !== null
                ) {
                    result._image_index = Number(section._image_index);
                }
                return result;
            });
        };
        const payload = {
            id: source.id ? String(source.id) : undefined,
            date: String(source.date ?? '').trim(),
            clientName: String(
                source.clientName ?? source.client_name ?? ''
            ).trim(),
            author: String(source.author ?? '').trim(),
            logo: normalizeImage(source.logo),
            planTitle: String(
                source.planTitle ?? source.plan_title ?? ''
            ).trim(),
            seriesTags: Array.isArray(source.seriesTags)
                ? source.seriesTags
                      .map((item) => String(item).trim())
                      .filter(Boolean)
                : [],
            categories: Array.isArray(source.categories)
                ? source.categories
                      .map((item) => String(item).trim())
                      .filter(Boolean)
                      .slice(0, 2)
                : [],
            description: String(source.description ?? '').trim(),
            social_media: normalizeSocialMedia(source.social_media),
            tableOfContents: normalizeTableOfContents(
                source.tableOfContents ?? source.table_of_contents
            ),
            sections: normalizeSections(source.sections)
        };
        return Object.fromEntries(
            Object.entries(payload).filter(([, value]) => value !== undefined)
        );
    };
    const normalizeCaseStudyPayload = (input) => {
        if (!input || typeof input !== 'object') {
            return {};
        }
        const payload = {
            ...input,
            id: input.id ? String(input.id) : undefined,
            title: String(input.title ?? '').trim(),
            description: String(input.description ?? '').trim(),
            categories: Array.isArray(input.categories)
                ? [...input.categories]
                : [],
            active: input.active ?? true,
            date: input.date ?? '',
            author: input.author ?? '',
            seriesTags: Array.isArray(input.seriesTags)
                ? [...input.seriesTags]
                : [],
            image: input.image ?? null,
            logo: input.logo ?? null,
            tableOfContents: normalizeTableOfContents(input.tableOfContents),
            sections: Array.isArray(input.sections)
                ? input.sections.map((section, index) => ({
                      id: section?.id ?? `section-${Date.now()}-${index}`,
                      type: section?.type ?? 'text',
                      title: String(section?.title ?? '').trim(),
                      content: String(section?.content ?? '').trim(),
                      image: section?.image ?? null,
                      imageFile:
                          section?.imageFile instanceof File
                              ? section.imageFile
                              : null
                  }))
                : []
        };
        return Object.fromEntries(
            Object.entries(payload).filter(([, value]) => value !== undefined)
        );
    };
    const normalizePayload = (input) => {
        if (collection === 'categories') {
            return normalizeCategoryPayload(input);
        }
        if (collection === 'caseStudies') {
            return normalizeCaseStudyPayload(input);
        }
        if (collection === 'case-study-detail') {
            return normalizeCaseStudyDetailPayload(input);
        }
        return clonePayload(input);
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
        if (type === 'case-study-detail') {
            return {
                loading: 'Updating case study detail...',
                success: 'Case Study detail updated successfully!',
                error: 'Update Case Study detail failed!'
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
        const isNew = collection === 'case-study-detail' ? false : !input.id;
        const messages = getToastMessages(collection, isNew);
        const toastId = toast.info(messages.loading, {
            timeout: false
        });
        try {
            const payload = normalizePayload(input);
            if (!payload || typeof payload !== 'object') {
                throw new Error('Invalid payload.');
            }
            if (collection === 'case-study-detail') {
                if (!Array.isArray(payload.social_media)) {
                    payload.social_media = [];
                }
                payload.social_media = payload.social_media.map((social) => ({
                    ...social,
                    icon: social?.icon
                        ? {
                              ...social.icon
                          }
                        : null
                }));
            }
            if (payload.id) {
                payload.id = String(payload.id);
            }
            const variables = {
                section: collection,
                input: '',
                action: isNew ? 'create' : 'update'
            };
            if (payload.id) {
                variables.id = String(payload.id);
            }
            if (collection === 'caseStudies') {
                const images = [];
                if (image instanceof File) {
                    payload.image = {
                        _image_index: images.length
                    };
                    images.push(image);
                }
                if (logo instanceof File) {
                    payload.logo = {
                        _image_index: images.length
                    };
                    images.push(logo);
                }
                if (Array.isArray(payload.sections)) {
                    payload.sections = payload.sections.map((section) => {
                        const { imageFile, ...sectionPayload } = section;
                        if (imageFile instanceof File) {
                            const imageIndex = images.length;
                            images.push(imageFile);
                            return {
                                ...sectionPayload,
                                image: {
                                    _image_index: imageIndex
                                }
                            };
                        }
                        return sectionPayload;
                    });
                }
                if (images.length > 0) {
                    variables.images = images;
                }
            }
            if (collection === 'case-study-detail') {
                if (Array.isArray(image)) {
                    const validImages = image.filter(
                        (file) => file instanceof File
                    );
                    if (validImages.length > 0) {
                        variables.images = validImages;
                    }
                } else if (image instanceof File) {
                    variables.images = [image];
                }
            }
            if (collection === 'banner') {
                if (image instanceof File) {
                    variables.images = [image];
                }
            }
            variables.input = JSON.stringify(payload);
            console.log('CASE STUDY VARIABLES:', variables);
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
            const message =
                error?.graphQLErrors?.[0]?.message ||
                error?.message ||
                messages.error;
            toast.error(message);
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
        if (collection === 'banner' || collection === 'case-study-detail') {
            const message =
                collection === 'banner'
                    ? 'Case Study banner does not support deletion.'
                    : 'Case Study detail does not support deletion.';
            toast.error(message);
            throw new Error(message);
        }
        loading.value = true;
        const isCategory = collection === 'categories';
        const toastId = toast.info(
            isCategory ? 'Deleting category...' : 'Deleting case study...',
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
                isCategory
                    ? 'Category deleted successfully!'
                    : 'Case Study deleted successfully!'
            );
            return parseResponse(response?.data?.deleteCaseStudy);
        } catch (error) {
            toast.dismiss(toastId);
            const message =
                error?.graphQLErrors?.[0]?.message ||
                error?.message ||
                (isCategory
                    ? 'Delete category failed!'
                    : 'Delete Case Study failed!');
            toast.error(message);
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
            const message =
                error?.graphQLErrors?.[0]?.message ||
                error?.message ||
                'Delete Case Study image failed!';
            toast.error(message);
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
        caseStudyDetail,
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
