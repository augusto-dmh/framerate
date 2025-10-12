<template>
  <div
    class="flex items-center justify-between border-t border-gray-20bg-white px-4 py-3 sm:px-6"
  >
    <div class="flex flex-1 justify-between sm:hidden">
      <Link
        v-if="previousUrl"
        :href="previousUrl"
        :preserve-scroll="preserveScroll"
        :only="only"
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >Previous
      </Link>
      <Link
        v-if="nextUrl"
        :href="nextUrl"
        :preserve-scroll="preserveScroll"
        :only="only"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >Next
      </Link>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          {{ " " }}
          <span class="font-medium">{{ meta.from }}</span>
          {{ " " }}
          to
          {{ " " }}
          <span class="font-medium">{{ meta.to }}</span>
          {{ " " }}
          of
          {{ " " }}
          <span class="font-medium">{{ meta.total }}</span>
          {{ " " }}
          results
        </p>
      </div>
      <div>
        <nav
          class="isolate bg-white inline-flex -space-x-px rounded-md shadow-xs"
          aria-label="Pagination"
        >
            <template v-for="(link, i) in meta.links" :key="`${link.url}-${i}`">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    :preserve-scroll="preserveScroll"
                    :only="only"
                    class="relative inline-flex items-c
                    enter first-of-type:rounded-l-md last-of-type:rounded-r-md px-3 py-2 focus:z-20 focus:outline-offset-0"
                    :class="{
                    'z-10 bg-indigo-600 text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600': link.active,
                    'text-gray-900 ring-1 ring-inset right-gray-300 hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : !link.active
                    }"
                    v-html="link.label"
                >
                </Link>
                <span
                  v-else
                  class="text-gray-900 ring-1 ring-inset right-gray-300 hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 px-3 py-2 focus-visible:outline-indigo-600"
                  v-html="link.label"
                />
            </template>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    meta: {
        type: Object,
        required: true,
    },
    preserveScroll: {
        type: Boolean,
        default: false,
    },
    only: {
        type: Array,
        default: () => [],
    },
});

const only = computed(() => props.only.length === 0 ? [] : [...props.only, 'jetstream']);
const previousUrl = computed(() => props.meta.links[0].url);
const nextUrl = computed(() => props.meta.links.toReversed()[0].url);
</script>
