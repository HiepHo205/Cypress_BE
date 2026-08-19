<script setup>
import {
    computed,
    onMounted,
    nextTick,
    ref,
    reactive
} from 'vue';

import {
    useQuery,
    useMutation
} from '@vue/apollo-composable';

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import { useToast } from 'vue-toastification';

import {
    GET_LOCATIONS
} from '@/graphql/queries/locations';

import {
    UPDATE_LOCATION
} from '@/graphql/mutations/locations';

const toast = useToast();

const { result, loading } =
    useQuery(GET_LOCATIONS);

const locations = computed(
    () => result.value?.locations ?? []
);

const selectedLocation =
    ref(null);

const showInfoPanel =
    ref(true);

const form = reactive({
    name: '',
    status: 'active'
});

const {
    mutate: updateLocation,
    loading: saving
} = useMutation(
    UPDATE_LOCATION,
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
        12
    );

    L.tileLayer(
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution:
                '&copy; OpenStreetMap'
        }
    ).addTo(map);

    map.on(
        'movestart',
        () => {
            showInfoPanel.value =
                false;
        }
    );

    map.on(
        'moveend',
        () => {

            setTimeout(
                () => {

                    if (
                        selectedLocation.value
                    ) {

                        showInfoPanel.value =
                            true;
                    }

                },
                300
            );
        }
    );

    const stop =
        setInterval(() => {

            if (
                locations.value.length
            ) {

                focusLocation(
                    locations.value[0]
                );

                clearInterval(
                    stop
                );
            }

        }, 300);
});

function focusLocation(
    location
) {

    if (!location) {
        return;
    }

    selectedLocation.value =
        location;

    form.name =
        location.name;

    form.status =
        location.status;

    showInfoPanel.value =
        true;

    const lat = Number(
        location.latitude
    );

    const lng = Number(
        location.longitude
    );

    map.flyTo(
        [lat, lng],
        16,
        {
            duration: 1
        }
    );

    if (marker) {

        marker.setLatLng(
            [lat, lng]
        );

    } else {

        marker = L.marker(
            [lat, lng]
        ).addTo(map);
    }
}

async function saveLocation() {

    if (
        !selectedLocation.value
    ) {
        return;
    }

    try {

        await updateLocation({
            id:
                selectedLocation.value.id,

            input: {
                name:
                    form.name,

                address:
                    selectedLocation.value.address,

                status:
                    form.status,

                latitude:
                    Number(
                        selectedLocation.value.latitude
                    ),

                longitude:
                    Number(
                        selectedLocation.value.longitude
                    )
            }
        });

        selectedLocation.value.name =
            form.name;

        selectedLocation.value.status =
            form.status;

        toast.success(
            'Location updated successfully'
        );

    } catch {

        toast.error(
            'Update failed'
        );
    }
}
</script>

<template>
    <div class="space-y-6">

        <div>
            <h1
                class="text-2xl font-bold"
            >
                Location Management
            </h1>
        </div>

        <div
            v-if="loading"
            class="bg-white rounded-lg shadow p-6"
        >
            Loading...
        </div>

        <div
            v-else
            class="bg-white rounded-lg shadow overflow-hidden"
        >
            <div
                class="flex h-[500px]"
            >

                <!-- Sidebar -->

                <div
                    class="w-80 border-r bg-gray-50 flex flex-col"
                >
                    <div
                        class="p-4 border-b bg-white"
                    >
                        <h2
                            class="font-semibold text-lg"
                        >
                            Locations
                        </h2>
                    </div>

                    <div
                        class="flex-1 overflow-y-auto"
                    >
                        <div
                            v-for="location in locations"
                            :key="
                                location.id
                            "
                            @click="
                                focusLocation(
                                    location
                                )
                            "
                            class="p-4 border-b cursor-pointer transition hover:bg-blue-50"
                            :class="{
                                'bg-blue-50 border-l-4 border-blue-600':
                                    selectedLocation?.id === location.id
                            }"
                        >
                            <div
                                class="font-medium"
                            >
                                {{
                                    location.name
                                }}
                            </div>

                            <div
                                class="text-xs mt-1"
                                :class="
                                    location.status === 'active'
                                        ? 'text-green-600'
                                        : 'text-gray-500'
                                "
                            >
                                {{
                                    location.status
                                }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map -->

                <div
                    class="flex-1 relative"
                >
                    <div
                        id="map"
                        class="w-full h-full"
                    />

                    <Transition
                        name="fade-panel"
                    >
                        <div
                            v-if="
                                selectedLocation &&
                                showInfoPanel
                            "
                            class="absolute left-4 bottom-4 w-80 bg-white rounded-xl shadow-xl border z-[1000]"
                        >
                            <div
                                class="p-4 space-y-3"
                            >
                                <div>
                                    <label
                                        class="block text-xs text-gray-500 mb-1"
                                    >
                                        Name
                                    </label>

                                    <input
                                        v-model="
                                            form.name
                                        "
                                        class="w-full border rounded-lg px-3 py-2 text-sm"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-xs text-gray-500 mb-1"
                                    >
                                        Status
                                    </label>

                                    <select
                                        v-model="
                                            form.status
                                        "
                                        class="w-full border rounded-lg px-3 py-2 text-sm"
                                    >
                                        <option
                                            value="active"
                                        >
                                            Active
                                        </option>

                                        <option
                                            value="inactive"
                                        >
                                            Inactive
                                        </option>
                                    </select>
                                </div>

                                <div
                                    class="text-xs text-gray-500"
                                >
                                    {{
                                        selectedLocation.address
                                    }}
                                </div>

                                <button
                                    @click="
                                        saveLocation
                                    "
                                    :disabled="
                                        saving
                                    "
                                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700"
                                >
                                    {{
                                        saving
                                            ? 'Saving...'
                                            : 'Save'
                                    }}
                                </button>
                            </div>
                        </div>
                    </Transition>

                </div>

            </div>
        </div>

    </div>
</template>

<style scoped>
.fade-panel-enter-active,
.fade-panel-leave-active {
    transition:
        opacity 0.25s ease,
        transform 0.25s ease;
}

.fade-panel-enter-from,
.fade-panel-leave-to {
    opacity: 0;
    transform: translateY(12px);
}

.fade-panel-enter-to,
.fade-panel-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>