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
                Create Plan
            </h1>
        </div>

        <form
            class="bg-white rounded-lg shadow p-6 space-y-4"
            @submit.prevent="submit"
        >
            <input
                v-model="form.plan_name"
                placeholder="Plan Name"
                class="w-full border rounded px-3 py-2"
            />

            <input
                v-model="form.price"
                placeholder="Price"
                class="w-full border rounded px-3 py-2"
            />

            <input
                v-model="form.workspace"
                placeholder="Workspace"
                class="w-full border rounded px-3 py-2"
            />

            <button
                class="w-full bg-blue-600 text-white py-2 rounded"
            >
                Create
            </button>
        </form>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useMutation } from '@vue/apollo-composable';

import { CREATE_PLAN } from '@/graphql/mutations/plans';
import { GET_PLANS } from '@/graphql/queries/plans';

const router = useRouter();

const form = reactive({
    plan_name: '',
    price: '',
    workspace: '',
});

const { mutate } = useMutation(
    CREATE_PLAN,
    {
        refetchQueries: [
            {
                query: GET_PLANS,
            },
        ],
    }
);

async function submit() {
    await mutate({
        input: {
            ...form,
        },
    });

    router.push('/admin/plans');
}
</script>