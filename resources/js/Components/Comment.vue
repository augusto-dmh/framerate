<script setup>
import { formatDate } from './Utilities/date';

    defineProps(['comment']);

    const formattedDate = (date) => formatDate(date);

    function preventWidow(text) {
        return text.replace(/\s(?=[^\s]*$)/, '&nbsp;');
    }
</script>

<template>
    <div class="flex items-center gap-4">
        <img :src="comment.user.profile_photo_url" :alt="comment.user.name" class="rounded-ful w-12 h-12" />
        <div class="flex gap-2 flex-col">
            <span class="text-sm break-all" v-html="preventWidow(comment.body)"></span>
            <span class="text-xs text-gray-600 block">By {{ comment.user.name }} {{ formattedDate(comment.created_at) }} ago</span>
        </div>
    </div>
</template>
