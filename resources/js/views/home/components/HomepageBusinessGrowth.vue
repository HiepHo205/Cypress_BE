<script setup lang="ts">
import { ref, watch } from 'vue';
import { useHomepage } from '../../../composables/home/useHomepage';
import { Save, Trash2, Plus } from 'lucide-vue-next';

const { loading, getSection, saveSection, removeItem } = useHomepage();
const isCreating = ref(false);
const businessGrowth = getSection('businessGrowth');

const sectionForm = ref({
    label: '',
    title: '',
    description: ''
});

const packageList = ref<any[]>([]);

const selectedPackage = ref<any>(null);

watch(
    businessGrowth,
    (value) => {
        console.log('BUSINESS GROWTH DATA:', value);

        if (!value) {
            return;
        }

        sectionForm.value = {
            label: value.label ?? '',
            title: value.title ?? '',
            description: value.description ?? ''
        };

        packageList.value = (value.packages ?? []).map(
            (item: any, index: number) => ({
                ...item,
                number: index + 1
            })
        );
        if (packageList.value.length) {
            selectedPackage.value = packageList.value[0];
        }
    },
    {
        immediate: true
    }
);

const saveInformation = async () => {
    await saveSection('businessGrowth', sectionForm.value);
};

const addPackage = () => {
    isCreating.value = true;

    selectedPackage.value = {
        number: packageList.value.length + 1,
        title: '',
        packageName: '',
        headline: '',
        description: '',
        color: '#2563eb',
        active: true
    };
};

const savePackage = async (item: any) => {
    const packages = isCreating.value
        ? [
              ...packageList.value,
              {
                  number: item.number,
                  title: item.title,
                  packageName: item.packageName,
                  headline: item.headline,
                  description: item.description,
                  color: item.color,
                  active: item.active
              }
          ]
        : packageList.value.map((pkg) =>
              pkg.number === item.number
                  ? {
                        number: item.number,
                        title: item.title,
                        packageName: item.packageName,
                        headline: item.headline,
                        description: item.description,
                        color: item.color,
                        active: item.active
                    }
                  : pkg
          );

    await saveSection('business_growth', {
        label: sectionForm.value.label,
        title: sectionForm.value.title,
        description: sectionForm.value.description,
        packages
    });

    isCreating.value = false;
};
const removePackage = async (item: any) => {
    if (!item) return;

    const updatedPackages = packageList.value
        .filter((pkg) => pkg.number !== item.number)
        .map((pkg) => ({
            number: pkg.number,
            title: pkg.title,
            packageName: pkg.packageName,
            headline: pkg.headline,
            description: pkg.description,
            color: pkg.color,
            active: pkg.active
        }));

    await saveSection(
        'business_growth',
        {
            label: sectionForm.value.label,
            title: sectionForm.value.title,
            description: sectionForm.value.description,
            packages: updatedPackages
        },
        null,
        'delete'
    );

    packageList.value = updatedPackages;
    selectedPackage.value = null;
};
</script>

<template>
    <section>
        <div class="rounded-xl border border-gray-100 bg-white">
            <div class="border-b border-gray-100 p-6">
                <h2 class="text-xl font-bold">Business Growth Information</h2>

                <p class="mt-1 text-sm text-gray-500">
                    Configure the content displayed above the packages.
                </p>
            </div>

            <div class="space-y-5 p-6">
                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Label
                    </label>

                    <input
                        v-model="sectionForm.label"
                        class="h-12 w-full rounded-xl border border-gray-100 px-4"
                    />
                </div> 

                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Title
                    </label>

                    <input
                        v-model="sectionForm.title"
                        class="h-12 w-full rounded-xl border border-gray-100 px-4"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Description
                    </label>

                    <textarea
                        v-model="sectionForm.description"
                        rows="4"
                        class="w-full rounded-xl border border-gray-100 px-4 py-3"
                    />
                </div>

                <div class="flex justify-end">
                    <button
                        @click="saveInformation"
                        :disabled="loading"
                        class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-white"
                    >
                        <Save :size="17" />

                    </button>
                </div>
            </div>
        </div>

        <div class="mb-6 mt-8 flex justify-between">
            <div>
                <h1 class="text-2xl font-bold">Business Growth Model</h1>

                <p class="text-sm text-gray-500">Manage growth packages</p>
            </div>

            <button
                @click="addPackage"
                class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white"
            >
                <Plus :size="18" />
            </button>
        </div>

        <div class="grid grid-cols-12 gap-5">
            <div class="col-span-4 space-y-3">
                <div
                    v-for="item in packageList"
                    :key="item.number"
                    @click="
                        selectedPackage = item;
                        isCreating = false;
                    "
                    class="cursor-pointer rounded-lg border border-gray-100 bg-white p-3"
                    :class="
                        selectedPackage?.number === item.number
                            ? 'border-blue-600 bg-blue-50'
                            : 'border-gray-200'
                    "
                >
                    <div class="flex gap-3">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-md text-white"
                            :style="{
                                background: item.color
                            }"
                        >
                            {{ item.number }}
                        </div>

                        <div>
                            <h3 class="font-semibold">
                                {{ item.title || 'Untitled' }}
                            </h3>

                            <p class="text-xs text-gray-500">
                                {{ item.packageName || 'No name' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-8">
                <div
                    v-if="selectedPackage"
                    class="rounded-xl border border-gray-100 bg-white p-5"
                >
                    <div class="mb-5 flex justify-between">
                        <h2 class="font-semibold">
                            {{
                                isCreating
                                    ? 'Create Package'
                                    : `Edit Package ${selectedPackage.number}`
                            }}
                        </h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Title
                            </label>

                            <input
                                v-model="selectedPackage.title"
                                placeholder="Enter package title"
                                class="w-full rounded-xl border border-gray-100 px-4 py-3"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Package Name
                            </label>

                            <input
                                v-model="selectedPackage.packageName"
                                placeholder="Enter package name"
                                class="w-full rounded-xl border px-4 py-3"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Headline
                            </label>

                            <input
                                v-model="selectedPackage.headline"
                                placeholder="Enter package headline"
                                class="w-full rounded-xl border  border-gray-100 px-4 py-3"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Description
                            </label>

                            <textarea
                                v-model="selectedPackage.description"
                                placeholder="Enter package description"
                                rows="5"
                                class="w-full rounded-xl border border-gray-100 px-4 py-3"
                            />
                        </div>

                        <div class="flex items-end justify-between pt-2">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Color
                                </label>

                                <input
                                    v-model="selectedPackage.color"
                                    type="color"
                                    class="h-10 w-16 cursor-pointer rounded border  border-gray-100 p-1"
                                />
                            </div>

                            <div class="flex items-center gap-3">
                                <button
                                    v-if="!isCreating"
                                    @click="removePackage(selectedPackage)"
                                    :disabled="loading"
                                    class="flex items-center gap-2 rounded-xl bg-red-500 px-5 py-2.5 text-white hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <Trash2 :size="16" />
                                </button>

                                <button
                                    @click="savePackage(selectedPackage)"
                                    :disabled="loading"
                                    class="flex items-center gap-2 rounded-xl bg-green-600 px-5 py-2.5 text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <Save :size="16" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
