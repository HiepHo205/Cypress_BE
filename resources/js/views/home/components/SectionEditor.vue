<script setup>
import { reactive } from "vue";

const section = reactive({
  key: "banner",
  title: "Work from anywhere",
  description: "Book workspace easily",
  primaryButton: {
    label: "Book a Service",
    url: "/booking",
  },
  secondaryButton: {
    label: "See how it works",
    url: "/about",
  },
});
</script>

<template>
  <div class="bg-white p-6 rounded-xl shadow space-y-6">
    <!-- TITLE -->
    <div>
      <label class="block text-sm font-semibold mb-2">Title</label>
      <input
        v-model="section.title"
        class="w-full border rounded-lg px-3 py-2"
      />
    </div>

    <!-- DESCRIPTION -->
    <div>
      <label class="block text-sm font-semibold mb-2">Description</label>
      <textarea
        v-model="section.description"
        class="w-full border rounded-lg px-3 py-2"
      />
    </div>

    <!-- PRIMARY BUTTON -->
    <div class="border rounded-xl p-4 space-y-2">
      <h4 class="font-semibold">Primary Button (Xanh)</h4>

      <input
        v-model="section.primaryButton.label"
        placeholder="Label"
        class="w-full border rounded px-3 py-2"
      />

      <input
        v-model="section.primaryButton.url"
        placeholder="URL"
        class="w-full border rounded px-3 py-2"
      />
    </div>

    <!-- SECONDARY BUTTON -->
    <div class="border rounded-xl p-4 space-y-2">
      <h4 class="font-semibold">Secondary Button (Xám)</h4>

      <input
        v-model="section.secondaryButton.label"
        placeholder="Label"
        class="w-full border rounded px-3 py-2"
      />

      <input
        v-model="section.secondaryButton.url"
        placeholder="URL"
        class="w-full border rounded px-3 py-2"
      />
    </div>

    <!-- PREVIEW -->
    <div>
      <h3 class="font-semibold mb-3">Preview</h3>

      <div class="space-y-3">
        <h2 class="text-xl font-bold">
          {{ section.title }}
        </h2>

        <p class="text-gray-600">
          {{ section.description }}
        </p>

        <div class="flex gap-3">
          <!-- Primary -->
          <a
            :href="section.primaryButton.url"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg"
          >
            {{ section.primaryButton.label || "Primary Button" }}
          </a>

          <!-- Secondary -->
          <a
            :href="section.secondaryButton.url"
            class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg"
          >
            {{ section.secondaryButton.label || "Secondary Button" }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>