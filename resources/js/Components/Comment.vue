<script setup>
import { computed } from 'vue';
import { formatDate } from './Utilities/date';
import { Link, router, usePage } from '@inertiajs/vue3';
import { HandThumbDownIcon, HandThumbUpIcon } from '@heroicons/vue/20/solid';

    const props = defineProps(['comment']);

    const preventWidow = (text) => text.replace(/\s(?=[^\s]*$)/, '&nbsp;');

    const emit = defineEmits(['edit', 'delete']);
</script>

<template>
    <div class="sm:flex w-full">
        <div class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
            <img :src="comment.user.profile_photo_url" :alt="comment.user.name" class="h-10 w-10 rounded-full" />
        </div>
        <div class="flex-1 flex flex-col">
            <div class="mt-1 prose prose-sm max-w-none" v-html="comment.html"></div>
            <span class="first-letter:uppercase block pt-1 text-xs text-gray-600">
                By {{ comment.user.name }} {{ formatDate(comment.created_at) }} |
                <span class="text-pink-500">{{ comment.likes_count }} likes</span>
            </span>
            <div class="mt-1 flex justify-end space-x-3">
                <div v-if="$page.props.auth.user">
                    <Link v-if="comment.can.like" preserve-scroll :href="route('likes.store', ['comment', comment.id])" method="post" class="inline-block hover:text-pink-500 text-gray-7000 transition-colors cursor-pointer">
                        <HandThumbUpIcon class="size-4 inline-block mr-1" />
                        <span class="sr-only">Like Comment</span>
                    </Link>
                    <Link v-else preserve-scroll :href="route('likes.destroy', ['comment', comment.id])" method="delete" class="inline-block hover:text-pink-500 text-gray-7000 transition-colors cursor-pointer">
                        <HandThumbDownIcon class="size-4 inline-block mr-1" />
                        <span class="sr-only">Unlike Comment</span>
                    </Link>
                </div>
                <form v-if="comment.can?.update" @submit.prevent="$emit('edit', comment.id)">
                    <button class="font-mono text-xs">Edit</button>
                </form>
                <form v-if="comment.can?.delete" @submit.prevent="$emit('delete', comment.id)">
                    <button class="font-mono text-red-700 text-xs">Delete</button>
                </form>
            </div>
        </div>
    </div>
</template>
