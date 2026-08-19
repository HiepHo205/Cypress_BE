<script setup>
import {
    reactive,
    onMounted,
    nextTick
} from 'vue';

import { useRouter } from 'vue-router';
import { useMutation } from '@vue/apollo-composable';

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import {
    CREATE_LOCATION
} from '@/graphql/mutations/locations';

import {
    GET_LOCATIONS
} from '@/graphql/queries/locations';

const router = useRouter();

const form = reactive({
    name: '',
    address: '',
    status: 'active',
    latitude: '',
    longitude: ''
});

const {
    mutate: createLocation,
    loading
} = useMutation(
    CREATE_LOCATION,
    {
        refetchQueries: [
            {
                query: GET_LOCATIONS
            }
        ]
    }
);

let map = null;
let marker = null;

onMounted(async () => {

    await nextTick();

    map = L.map('map').setView(
        [16.0544, 108.2022],
        13
    );

    L.tileLayer(
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }
    ).addTo(map);

    map.on('click', event => {

        form.latitude =
            event.latlng.lat.toFixed(6);

        form.longitude =
            event.latlng.lng.toFixed(6);

        if (marker) {

            marker.setLatLng(
                event.latlng
            );

        } else {

            marker = L.marker(
                event.latlng
            ).addTo(map);
        }
    });
});

async function submit() {

    try {

        await createLocation({
            input: {
                name: form.name,
                address: form.address,
                status: form.status,
                latitude: Number(form.latitude),
                longitude: Number(form.longitude)
            }
        });

        router.push(
            '/admin/locations'
        );

    } catch (error) {

        console.error(error);
    }
}
</script>

<template>
    <div class="max-w-5xl mx-auto">

        <div class="mb-6">
            <h1 class="text-2xl font-bold">
                Create Location
            </h1>

            <p class="text-gray-500 mt-1">
                Select a location on the map and save it.
            </p>
        </div>

        <form
            class="bg-white rounded-lg shadow p-6 space-y-6"
            @submit.prevent="submit"
        >
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Location Name
                </label>

                <input
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                />
            </div>

            <div>
                <label
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Address
                </label>

                <input
                    v-model="form.address"
                    type="text"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                />
            </div>

            <div>
                <label
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Status
                </label>

                <select
                    v-model="form.status"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                >
                    <option value="active">
                        Active
                    </option>

                    <option value="inactive">
                        Inactive
                    </option>
                </select>
            </div>

            <div>
                <label
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Select Location On Map
                </label>

                <div
                    id="map"
                    class="w-full h-[450px] rounded-lg border"
                />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Latitude
                    </label>

                    <input
                        v-model="form.latitude"
                        readonly
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50"
                    />
                </div>

                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Longitude
                    </label>

                    <input
                        v-model="form.longitude"
                        readonly
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50"
                    />
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="loading"
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                >
                    {{
                        loading
                            ? 'Saving...'
                            : 'Create Location'
                    }}
                </button>
            </div>
        </form>
    </div>
</template>