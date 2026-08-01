<template>
    <div class="max-w-lg">
        <div class="mb-6">
            <router-link
                to="/admin/plans"
                class="text-blue-600"
            >
                ← Back
            </router-link>

            <h1 class="text-2xl font-bold mt-2">
                Edit Plan
            </h1>
        </div>

        <form
            v-if="ready"
            class="bg-white rounded-lg shadow p-6 space-y-4"
            @submit.prevent="submit"
        >
            <input
                v-model="form.plan_name"
                class="w-full border rounded px-3 py-2"
            />

            <input
                v-model="form.price"
                class="w-full border rounded px-3 py-2"
            />

            <input
                v-model="form.workspace"
                class="w-full border rounded px-3 py-2"
            />

            <button
                class="w-full bg-blue-600 text-white py-2 rounded"
            >
                Save
            </button>
        </form>
    </div>
</template>

<script setup>
import {
    reactive,
    watch,
    computed
} from 'vue';

import { useRouter } from 'vue-router';
import {
    useMutation,
    useQuery
} from '@vue/apollo-composable';

import {
    GET_PLAN,
    GET_PLANS
} from '@/graphql/queries/plans';

import {
    UPDATE_PLAN
} from '@/graphql/mutations/plans';

const props = defineProps({
    id: {
        type: String,
        required: true
    }
});

const router = useRouter();

const form = reactive({
    plan_name: '',
    price: '',
    workspace: '',
});

const { result } = useQuery(
    GET_PLAN,
    () => ({
        id: props.id
    })
);

const ready = computed(
    () => !!result.value?.plan
);

watch(
    () => result.value?.plan,
    plan => {

        if (!plan) {
            return;
        }

        form.plan_name =
            plan.plan_name;

        form.price =
            plan.price;

        form.workspace =
            plan.workspace;
    },
    {
        immediate: true
    }
);

const { mutate } = useMutation(
    UPDATE_PLAN,
    {
        refetchQueries: [
            {
                query: GET_PLANS
            }
        ]
    }
);

async function submit() {

    await mutate({
        id: props.id,
        input: {
            ...form
        }
    });

    router.push('/admin/plans');
}
</script>