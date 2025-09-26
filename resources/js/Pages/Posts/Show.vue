<template>
    <AppLayout :title="post.title">
        <Container>
            <h1 class="text-2xl font-bold">{{ post.title }}</h1>
            <p class="mb-6 text-sm text-gray-500">{{ postDateFormatted }} by {{ post.user.name }}</p>

            <article class="mt-6 prose prose-sm max-w-none" v-html="post.html">
            </article>

            <div class="mt-12">
                <h2 class="text-xl font-semibold">Comments</h2>

                <form v-if="$page.props.auth.user" @submit.prevent="() => commentIdBeingEdited ? updateComment() : addComment()">
                    <div>
                        <InputLabel for="body" class="sr-only">Comment</InputLabel>
                        <MarkdownEditor ref="commentTextAreaRef" id="body" v-model="commentForm.body" editorClass="min-h-[160px]" />
                        <InputError :message="commentForm.errors.body" />
                    </div>

                    <PrimaryButton type="submit" class="mt-3" :disabled="commentForm.processing" v-text="commentIdBeingEdited ? 'Update Comment' : 'Add Comment'" />
                    <SecondaryButton v-if="commentBeingEdit" @click="cancelEditComment" class="ml-2">Cancel</SecondaryButton>
                </form>

                <ul class="divide-y">
                    <li v-for="comment in comments.data" :key="comment.id" class="flex gap-4 px-2 py-4">
                        <Comment @edit="editComment" @delete="deleteComment" :comment="comment" />
                    </li>
                </ul>

                <Pagination :meta="comments.meta" :preserveScroll="true" :only="['comments']" />
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Comment from '@/Components/Comment.vue';
import Container from '@/Components/Container.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import MarkdownEditor from '@/Components/MarkdownEditor.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useConfirm } from '@/Components/Utilities/Composables/useConfirm';
import { formatDate } from '@/Components/Utilities/date';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps(['post', 'comments']);

const postDateFormatted = computed(() => formatDate(props.post.created_at));

const commentForm = useForm({
    'body': '',
});

const commentTextAreaRef = ref(null);
const commentIdBeingEdited = ref(null);
const commentBeingEdit = computed(() => props.comments.data.find(comment => comment.id === commentIdBeingEdited.value));

const editComment = (commentId) => {
    commentIdBeingEdited.value = commentId;
    commentForm.body = commentBeingEdit.value?.body;
    commentTextAreaRef.value?.focus();
}

const cancelEditComment = () => {
    commentIdBeingEdited.value = null;
    commentForm.reset();
}

const addComment = () => {
    commentForm.post(
        route('posts.comments.store', props.post.id),
        {
            preserveScroll: true,
            onSuccess: () => commentForm.body = '',
        },
    );
};

const { confirmation } = useConfirm();

const updateComment = async () => {
    if (! await confirmation('Are you sure you want to update this comment?')) {
        setTimeout(() => commentTextAreaRef.value?.focus(), 250);
        return;
    }

    commentForm.put(route('comments.update', commentIdBeingEdited.value), {
        comment: commentIdBeingEdited.value,
        page: props.comments.meta.current_page,
        onSuccess: cancelEditComment,
        preserveScroll: true,
    });
}

const deleteComment = async (commentId) => {
    if (! await confirmation('Are you sure you want to delete this comment?')) {
        return;
    }

    router.delete(route('comments.destroy', { comment: commentId, page: props.comments.meta.current_page }), {
        preserveScroll: true,
    });
}
</script>
