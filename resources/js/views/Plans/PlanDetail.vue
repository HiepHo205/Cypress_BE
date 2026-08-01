<template>
    <div
        v-if="isLoading"
        class="flex justify-center items-center h-96"
    >
        <div
            class="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"
        />
    </div>

    <div
        v-else-if="form.id"
    >

        <!-- HEADER -->

        <div
            class="flex justify-between items-center mb-6"
        >
            <router-link
                to="/admin/plans"
                class="text-blue-600 hover:text-blue-800"
            >
                ← Back to Plans
            </router-link>

            <button
                @click="savePlan"
                :disabled="saving"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <div
                    v-if="saving"
                    class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                />

                <Save
                    v-else
                    :size="18"
                />

                {{ saving ? 'Saving...' : 'Save' }}
            </button>
        </div>

        <!-- PLAN INFO -->

        <div
            class="bg-white rounded-lg shadow p-6"
        >
            <h2
                class="text-xl font-semibold mb-4"
            >
                Plan Information
            </h2>

            <div class="grid gap-4">

                <div>
                    <label
                        class="block text-sm font-medium mb-1"
                    >
                        Plan Name
                    </label>

                    <input
                        v-model="form.plan_name"
                        class="w-full border rounded px-3 py-2"
                    />
                </div>

                <div>
                    <label
                        class="block text-sm font-medium mb-1"
                    >
                        Price
                    </label>

                    <input
                        v-model="form.price"
                        class="w-full border rounded px-3 py-2"
                    />
                </div>

            </div>
        </div>

        <!-- CORE -->

        <div
            class="bg-white rounded-lg shadow p-6 mt-6"
        >
            <h2
                class="text-xl font-semibold mb-6"
            >
                Core Amenities
            </h2>

            <div
                v-for="feature in coreFeatures"
                :key="feature.id"
                class="border rounded-lg p-4 mb-4"
            >
                <div
                    class="flex items-center justify-between mb-4"
                >
                    <div
                        class="flex items-center gap-3"
                    >
                        <component
                            :is="getFeatureIcon(feature.name)"
                            :size="20"
                            class="text-blue-600"
                        />

                        <h3
                            class="font-semibold text-lg"
                        >
                            {{ feature.name }}
                        </h3>
                    </div>

                    <input
                        type="checkbox"
                        :checked="isSelected(feature.id)"
                        @change="toggleFeature(feature)"
                        class="w-4 h-4"
                    />
                </div>

                <template
                    v-if="isSelected(feature.id)"
                >
                    <div
                        v-for="(
                            benefit,
                            index
                        ) in getSelectedFeature(feature.id)?.benefits"
                        :key="
                            benefit.id ??
                            `new-${index}`
                        "
                        class="flex gap-2 mb-2"
                    >
                        <input
                            v-model="benefit.value"
                            class="flex-1 border rounded px-3 py-2"
                        />

                        <button
                            @click="
                                removeBenefit(
                                    getSelectedFeature(feature.id),
                                    index
                                )
                            "
                            class="text-red-600"
                        >
                            <Trash2
                                :size="18"
                            />
                        </button>
                    </div>

                    <button
                        @click="
                            addBenefit(
                                getSelectedFeature(feature.id)
                            )
                        "
                        class="mt-2 flex items-center gap-2 text-blue-600"
                    >
                        <Plus :size="16" />
                        Add Benefit
                    </button>
                </template>
            </div>
        </div>

        <!-- USP -->

        <div
            class="bg-white rounded-lg shadow p-6 mt-6"
        >
            <h2
                class="text-xl font-semibold mb-6"
            >
                Unique Selling Points
            </h2>

            <div
                v-for="feature in uspFeatures"
                :key="feature.id"
                class="border rounded-lg p-4 mb-4"
            >
                <div
                    class="flex items-center justify-between mb-4"
                >
                    <div
                        class="flex items-center gap-3"
                    >
                        <component
                            :is="getFeatureIcon(feature.name)"
                            :size="20"
                            class="text-purple-600"
                        />

                        <h3
                            class="font-semibold text-lg"
                        >
                            {{ feature.name }}
                        </h3>
                    </div>

                    <input
                        type="checkbox"
                        :checked="isSelected(feature.id)"
                        @change="toggleFeature(feature)"
                        class="w-4 h-4"
                    />
                </div>

                <template
                    v-if="isSelected(feature.id)"
                >
                    <div
                        v-for="(
                            benefit,
                            index
                        ) in getSelectedFeature(feature.id)?.benefits"
                        :key="
                            benefit.id ??
                            `new-${index}`
                        "
                        class="flex gap-2 mb-2"
                    >
                        <input
                            v-model="benefit.value"
                            class="flex-1 border rounded px-3 py-2"
                        />

                        <button
                            @click="
                                removeBenefit(
                                    getSelectedFeature(feature.id),
                                    index
                                )
                            "
                            class="text-red-600"
                        >
                            <Trash2
                                :size="18"
                            />
                        </button>
                    </div>

                    <button
                        @click="
                            addBenefit(
                                getSelectedFeature(feature.id)
                            )
                        "
                        class="mt-2 flex items-center gap-2 text-blue-600"
                    >
                        <Plus :size="16" />
                        Add Benefit
                    </button>
                </template>
            </div>
        </div>

    </div>
</template>

<script setup>
import {
    computed,
    reactive,
    watch
} from 'vue';

import {
    useQuery,
    useMutation
} from '@vue/apollo-composable';

import { useToast } from 'vue-toastification';

import {
    Save,
    Plus,
    Trash2,
    Building2,
    Presentation,
    Printer,
    Monitor,
    Megaphone,
    BriefcaseBusiness,
    Code2,
    Bot,
    Landmark
} from 'lucide-vue-next';

import {
    GET_PLAN,
    GET_FEATURES
} from '@/graphql/queries/plans';

import {
    UPDATE_PLAN
} from '@/graphql/mutations/plans';

const toast = useToast();

const props = defineProps({
    id: String
});

const {
    result,
    loading: loadingPlan,
} = useQuery(
    GET_PLAN,
    () => ({
        id: props.id
    })
);

const {
    result: featuresResult,
    loading: loadingFeatures
} = useQuery(
    GET_FEATURES
);

const isLoading = computed(
    () =>
        loadingPlan.value ||
        loadingFeatures.value
);

const form = reactive({
    id: '',
    plan_name: '',
    price: '',
    features: []
});

watch(
    () => result.value?.plan,
    (plan) => {

        if (!plan) return;

        form.id = plan.id;

        form.plan_name =
            plan.plan_name;

        form.price =
            plan.price;

        form.features =
            JSON.parse(
                JSON.stringify(
                    plan.features ?? []
                )
            );
    },
    {
        immediate: true
    }
);

const allFeatures =
    computed(
        () =>
            featuresResult.value
                ?.features ?? []
    );

const coreFeatures =
    computed(
        () =>
            allFeatures.value.filter(
                feature =>
                    feature.group ===
                    'core'
            )
    );

const uspFeatures =
    computed(
        () =>
            allFeatures.value.filter(
                feature =>
                    feature.group ===
                    'usp'
            )
    );

function getSelectedFeature(
    featureId
) {
    return form.features.find(
        feature =>
            String(feature.id) ===
            String(featureId)
    );
}

function isSelected(
    featureId
) {
    return !!getSelectedFeature(
        featureId
    );
}

function toggleFeature(
    feature
) {

    const index =
        form.features.findIndex(
            item =>
                String(item.id) ===
                String(feature.id)
        );

    if (index > -1) {

        form.features.splice(
            index,
            1
        );

        return;
    }

    form.features.push({
        id: feature.id,
        name: feature.name,
        group: feature.group,
        benefits: []
    });
}

function addBenefit(
    feature
) {

    if (!feature) {
        return;
    }

    feature.benefits.push({
        id: null,
        value: ''
    });
}

function removeBenefit(
    feature,
    index
) {

    if (!feature) {
        return;
    }

    feature.benefits.splice(
        index,
        1
    );
}

const {
    mutate: updatePlanMutation,
    loading: saving
} = useMutation(
    UPDATE_PLAN
);

async function savePlan() {

    if (saving.value) {
        return;
    }
    try {

        for (const feature of form.features) {

            for (const benefit of feature.benefits) {

                if (
                    benefit.value === null ||
                    benefit.value === undefined
                ) {

                    toast.error(
                        `${feature.name}: Benefit cannot be empty`
                    );

                    return;
                }

                if (
                    !String(
                        benefit.value
                    ).trim()
                ) {

                    toast.error(
                        `${feature.name}: Benefit cannot be empty`
                    );

                    return;
                }
            }
        }

        const payload = {

            id: form.id,

            input: {

                plan_name:
                    form.plan_name?.trim(),

                price:
                    form.price?.trim(),

                features:
                    form.features.map(
                        feature => ({

                            id:
                                feature.id,

                            benefits:
                                feature.benefits.map(
                                    benefit => ({

                                        id:
                                            benefit.id,

                                        value:
                                            String(
                                                benefit.value ?? ''
                                            ).trim()
                                    })
                                )
                        })
                    )
            }
        };

        await updatePlanMutation(
            payload
        );

        toast.success(
            'Plan updated successfully'
        );

    } catch (error) {

        const graphQLError =
            error?.graphQLErrors?.[0]
                ?.message;

        toast.error(
            graphQLError ||
            error.message ||
            'Failed to update plan'
        );
    }
}

const featureIcons = {
    Workspace:
        Building2,

    'Meeting Room':
        Presentation,

    'Printing Service':
        Printer,

    'Screen Rental':
        Monitor,

    'Marketing Services':
        Megaphone,

    'Operations Consulting':
        BriefcaseBusiness,

    'Software Consulting':
        Code2,

    'AI Agent Consulting':
        Bot,

    'Financial Advisor':
        Landmark
};

function getFeatureIcon(
    name
) {
    return (
        featureIcons[
            name
        ] || Building2
    );
}
</script>