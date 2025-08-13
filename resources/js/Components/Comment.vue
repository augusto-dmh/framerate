<script setup>
import { computed } from 'vue';
import { formatDate } from './Utilities/date';
import { router, usePage } from '@inertiajs/vue3';

    const props = defineProps(['comment']);

    const formattedDate = (date) => formatDate(date);

    const preventWidow = (text) => text.replace(/\s(?=[^\s]*$)/, '&nbsp;');

    const deleteComment = () =>
        router.delete(route('comments.destroy', props.comment.id), {
            preserveScroll: true,
        });
</script>

<template>
    <div class="flex items-center gap-4">
        <img :src="comment.user.profile_photo_url" :alt="comment.user.name" class="rounded-ful w-12 h-12" />
        <div class="flex gap-2 flex-col">
            <p class="mt-1 break-all" v-html="preventWidow(comment.body)"></p>
            <span class="pt-1 text-xs text-gray-600 block">By {{ comment.user.name }} {{ formattedDate(comment.created_at) }} ago</span>
            <div class="mt-1">
                <form v-if="comment.can.delete" @submit.prevent="deleteComment">
                    <button>Delete</button>
                </form>
            </div>
        </div>
    </div>
</template>
