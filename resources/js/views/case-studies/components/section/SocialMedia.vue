<script setup>
import { computed, reactive, watch } from 'vue';
import { Save, Plus, Trash2, Upload, X, ArrowLeft } from 'lucide-vue-next';
import { useRoute, useRouter } from 'vue-router';
import { useCaseStudy } from '@/composables/caseStudy/useCaseStudy';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({
            social_media: []
        })
    }
});

const emit = defineEmits(['saved']);

const route = useRoute();
const router = useRouter();

const form = reactive({
    social_media: []
});

const { loading: saving, updateItem } = useCaseStudy('case-study-detail');

const caseStudyId = computed(() => {
    return String(route.params.id || '').trim();
});

const normalizeIcon = (icon) => {
    if (!icon) {
        return {
            url: '',
            public_id: ''
        };
    }

    if (typeof icon === 'string') {
        try {
            const parsed = JSON.parse(icon);

            if (parsed && typeof parsed === 'object') {
                return {
                    url: parsed.url ?? '',
                    public_id: parsed.public_id ?? ''
                };
            }

            return {
                url: icon,
                public_id: ''
            };
        } catch {
            return {
                url: icon,
                public_id: ''
            };
        }
    }

    if (typeof icon === 'object') {
        return {
            url: icon.url ?? '',
            public_id: icon.public_id ?? ''
        };
    }

    return {
        url: '',
        public_id: ''
    };
};

watch(
    () => props.data,
    (data) => {
        if (!caseStudyId.value) {
            form.social_media = [];
            return;
        }

        let detail = data;

        if (data?.caseStudyDetail) {
            detail = data.caseStudyDetail;
        }

        if (detail?.data?.caseStudyDetail) {
            detail = detail.data.caseStudyDetail;
        }

        if (typeof detail === 'string') {
            try {
                detail = JSON.parse(detail);
            } catch (error) {
                form.social_media = [];
                return;
            }
        }

        const socialMedia = detail?.social_media;

        if (!Array.isArray(socialMedia)) {
            form.social_media = [];
            return;
        }

        form.social_media = socialMedia.map((item) => ({
            name: item?.name ?? '',

            icon: normalizeIcon(item?.icon),

            url: item?.url ?? '',

            file: null
        }));
    },
    {
        immediate: true,
        deep: true
    }
);
const goBack = () => {
    router.push({
        name: 'case-studies.management'
    });
};

const addSocialMedia = () => {
    form.social_media.push({
        name: '',
        icon: {
            url: '',
            public_id: ''
        },
        url: '',
        file: null
    });
};

const removeSocialMedia = (index) => {
    const social = form.social_media[index];

    if (social?.icon?.url?.startsWith('blob:')) {
        URL.revokeObjectURL(social.icon.url);
    }

    form.social_media.splice(index, 1);
};

const handleIconChange = (event, index) => {
    const file = event.target.files?.[0];

    event.target.value = '';

    if (!file) {
        return;
    }

    if (!file.type?.startsWith('image/')) {
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        return;
    }

    const social = form.social_media[index];

    if (!social) {
        return;
    }

    if (social.icon?.url?.startsWith('blob:')) {
        URL.revokeObjectURL(social.icon.url);
    }

    social.file = file;

    social.icon = {
        url: URL.createObjectURL(file),
        public_id: ''
    };
};

const removeIcon = (index) => {
    const social = form.social_media[index];

    if (!social) {
        return;
    }

    if (social.icon?.url?.startsWith('blob:')) {
        URL.revokeObjectURL(social.icon.url);
    }

    social.file = null;

    social.icon = {
        url: '',
        public_id: ''
    };
};

const validateSocialMedia = () => {
    if (!caseStudyId.value) {
        return false;
    }

    for (const social of form.social_media) {
        if (!social.name?.trim()) {
            return false;
        }

        if (!social?.icon?.url) {
            return false;
        }

        if (social.url?.trim()) {
            try {
                new URL(social.url.trim());
            } catch {
                return false;
            }
        }
    }

    return true;
};

const save = async () => {
    const id = String(route.params.id || '').trim();

    if (!id) {
        return;
    }

    if (!validateSocialMedia()) {
        return;
    }

    try {
        const images = [];

        const socialMedia = form.social_media.map((social) => {
            let imageIndex = null;

            if (social.file instanceof File) {
                imageIndex = images.length;
                images.push(social.file);
            }

            return {
                name: social.name.trim(),

                icon: social.file
                    ? {
                          url: '',
                          public_id: ''
                      }
                    : {
                          url: social.icon?.url ?? '',
                          public_id: social.icon?.public_id ?? ''
                      },

                url: social.url?.trim() ?? '',

                ...(imageIndex !== null
                    ? {
                          _image_index: imageIndex
                      }
                    : {})
            };
        });

        await updateItem(
            {
                id,
                social_media: socialMedia
            },
            images
        );

        emit('saved');
    } catch {}
};
</script>

<template>
    <form class="mt-5" @submit.prevent="save">
        <div class="rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 px-5 py-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            title="Back to Case Studies"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600"
                            @click="goBack"
                        >
                            <ArrowLeft :size="18" />
                        </button>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-800">
                                Social Media Icons
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Manage social media icons and URLs.
                            </p>
                            <p class="mt-1 text-[10px] text-slate-400">
                                Case Study ID:
                                {{ caseStudyId }}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700"
                        @click="addSocialMedia"
                    >
                        <Plus :size="15" />
                        Add Social Media
                    </button>
                </div>
            </div>

            <div class="p-5">
                <div
                    v-if="form.social_media.length"
                    class="grid grid-cols-5 gap-4"
                >
                    <div
                        v-for="(social, index) in form.social_media"
                        :key="index"
                        class="rounded-xl border border-slate-200 bg-slate-50 p-3"
                    >
                        <div class="flex h-9 items-center gap-2">
                            <div
                                class="relative flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-white"
                            >
                                <img
                                    v-if="social.icon?.url"
                                    :src="social.icon.url"
                                    :alt="social.name || 'Social media icon'"
                                    class="h-5 w-5 object-contain"
                                />

                                <label
                                    v-else
                                    class="flex h-full w-full cursor-pointer items-center justify-center text-blue-600"
                                >
                                    <Upload :size="15" />

                                    <input
                                        type="file"
                                        accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                                        class="hidden"
                                        @change="
                                            handleIconChange($event, index)
                                        "
                                    />
                                </label>

                                <button
                                    v-if="social.icon?.url"
                                    type="button"
                                    class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-white"
                                    @click="removeIcon(index)"
                                >
                                    <X :size="8" />
                                </button>
                            </div>

                            <input
                                v-model="social.name"
                                type="text"
                                maxlength="100"
                                placeholder="Facebook"
                                class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            />

                            <button
                                type="button"
                                class="shrink-0 rounded-lg p-1.5 text-red-500 transition hover:bg-red-50"
                                @click="removeSocialMedia(index)"
                            >
                                <Trash2 :size="14" />
                            </button>
                        </div>

                        <div class="mt-2">
                            <input
                                v-model="social.url"
                                type="url"
                                placeholder="https://facebook.com/..."
                                class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            />
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center"
                >
                    <p class="text-sm text-slate-400">No social media added.</p>

                    <button
                        type="button"
                        class="mt-3 text-xs font-semibold text-blue-600 hover:text-blue-700"
                        @click="addSocialMedia"
                    >
                        Add Social Media
                    </button>
                </div>
            </div>

            <div
                class="flex items-center justify-end border-t border-slate-100 bg-slate-50/50 px-5 py-3"
            >
                <button
                    type="submit"
                    :disabled="saving || !caseStudyId"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <Save
                        :size="16"
                        :class="{
                            'animate-pulse': saving
                        }"
                    />
                </button>
            </div>
        </div>
    </form>
</template>
