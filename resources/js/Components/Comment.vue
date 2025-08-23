<script setup>
import { computed, onBeforeUnmount } from 'vue';
import { formatDate } from './Utilities/date';
import { router, usePage } from '@inertiajs/vue3';
import TextArea from '@/Components/TextArea.vue';
import { debounce } from './Utilities/debounce';

    const props = defineProps({
        comment: Object,
        isBeingEdited: Boolean,
    });

    const emit = defineEmits(['delete', 'edit', 'saveEdit', 'cancelEdit', 'updatePreview']);

    const debouncedUpdate = debounce((value) => {
        emit('updatePreview', value);
    }, 300);

    const formattedDate = (date) => formatDate(date);

    const preventWidow = (text) => text.replace(/\s(?=[^\s]*$)/, '&nbsp;');

    const onEditing = (e) => debouncedUpdate(e.target.value);

    onBeforeUnmount(() => {
        debouncedUpdate.cancel();
    });
</script>

<template>
    <div class="sm:flex w-full">
        <div class="mb-4 flex-shrink-0 sm:mb-0 sm:mr-4">
            <img :src="comment.user.profile_photo_url" :alt="comment.user.name" class="h-10 w-10 rounded-full" />
        </div>
        <div class="flex-1 flex flex-col">
            <div v-if="isBeingEdited">
                <TextArea
                    class="w-full border rounded p-2 "
                    :value="comment.body"
                    @input="onEditing"
                    rows="3"
                ></TextArea>
            </div>
            <p v-else class="mt-1 break-all" v-html="preventWidow(comment.body)"></p>

            <span class="first-letter:uppercase block pt-1 text-xs text-gray-600">
                By {{ comment.user.name }} {{ formattedDate(comment.created_at) }} ago
            </span>

            <div class="mt-1 flex justify-end items-center gap-2">
                <form v-if="!isBeingEdited && comment.can?.update" @submit.prevent="$emit('edit', comment)">
                    <button class="font-mono text-blue-600 text-xs">Edit</button>
                </form>

                <div v-if="isBeingEdited" class="flex gap-2">
                    <button class="font-mono text-green-600 text-xs" @click.prevent="$emit('saveEdit')">Save</button>
                    <button class="font-mono text-gray-600 text-xs" @click.prevent="$emit('cancelEdit')">Cancel</button>
                </div>

                <form v-if="comment.can?.delete" @submit.prevent="$emit('delete', comment.id)">
                    <button class="font-mono text-red-700 text-xs">Delete</button>
                </form>
            </div>
        </div>
    </div>
</template>
