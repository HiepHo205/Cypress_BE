<script setup lang="ts">
import { Save, RefreshCcw } from 'lucide-vue-next';
import { reactive, ref, watch } from 'vue';
import { useHomepage } from '../../../composables/home/useHomepage';

const { loading, getSection, saveSection } = useHomepage();
const activeTab = ref('content');

const offer = reactive({
  title: '',
  subtitle: '',
  buttonText: '',
  buttonUrl: '',
  expiryDate: '',
  seats: ''
});

const sectionData = getSection('launchOffer');

const loadOffer = (data: any) => {
  if (!data) {
    return;
  }

  offer.title = data.title ?? '';
  offer.subtitle = data.subtitle ?? '';
  offer.buttonText = data.buttonText ?? '';
  offer.buttonUrl = data.buttonUrl ?? '';
  offer.expiryDate = data.expiryDate ?? '';
  offer.seats = data.seats ?? '';
};

watch(
  sectionData,
  (data) => {
    loadOffer(data);
  },
  {
    deep: true,
    immediate: true
  }
);

const handleSave = async () => {
  await saveSection('launch_offer', {
    title: offer.title,
    subtitle: offer.subtitle,
    buttonText: offer.buttonText,
    buttonUrl: offer.buttonUrl,
    expiryDate: offer.expiryDate,
    seats: offer.seats
  });
};
</script>

<template>
  <div class="rounded-3xl bg-white p-8 shadow-sm">
    <div>
      <h2 class="text-3xl font-semibold text-gray-900">Launch Offer Manager</h2>
      <p class="mt-2 text-gray-500">Edit the homepage offer banner content and preview the result live.</p>
    </div>

    <div class="mt-8">

      <div class="mt-8">
        <div v-if="activeTab === 'content'" class="space-y-6">
          <div class="rounded-3xl border border-gray-200 bg-gray-50 p-6">
            <h3 class="text-xl font-semibold text-gray-900">Offer Content</h3>
            <div class="mt-6 space-y-4">
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Title</label>
                <input v-model="offer.title" class="w-full rounded-xl border border-gray-300 px-4 py-3" />
              </div>

              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Subtitle</label>
                <textarea v-model="offer.subtitle" rows="4" class="w-full rounded-xl border border-gray-300 px-4 py-3"></textarea>
              </div>

              <div class="grid gap-4 lg:grid-cols-2">
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700">Button text</label>
                  <input v-model="offer.buttonText" class="w-full rounded-xl border border-gray-300 px-4 py-3" />
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700">Button URL</label>
                  <input v-model="offer.buttonUrl" class="w-full rounded-xl border border-gray-300 px-4 py-3" />
                </div>
              </div>

              <div class="grid gap-4 lg:grid-cols-2">
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700">Expiry date</label>
                  <input
                    type="date"
                    v-model="offer.expiryDate"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3"
                  />
                </div>
                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700">Seats label</label>
                  <input v-model="offer.seats" class="w-full rounded-xl border border-gray-300 px-4 py-3" />
                </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button
              @click="handleSave"
              :disabled="loading"
              class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
            >
              <Save :size="18" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
