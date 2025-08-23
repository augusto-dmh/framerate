<template>
    <AppLayout :title="post.title">
        <Container>
            <h1 class="text-2xl font-bold mb-4">{{ post.title }}</h1>
            <p>{{ post.body }}</p>
            <p>Publicado: {{ postDateFormatted }}</p>

            <div class="mt-12 mt-4">
                <h2 class="text-xl font-semibold">Comments</h2>

                <form v-if="$page.props.auth.user" @submit.prevent="addComment">
                    <div>
                        <InputLabel for="body" class="sr-only">Comment</InputLabel>
                        <TextArea id="body" v-model="commentForm.body" />
                        <InputError :message="commentForm.errors.body" />
                    </div>

                    <PrimaryButton type="submit" class="mt-3" :disabled="commentForm.processing">
                        Add comment
                    </PrimaryButton>
                </form>

                <ul class="divide-y">
                    <li v-for="comment in comments" :key="comment.id" class="flex gap-4 px-2 py-4">
                        <Comment
                            :comment="comment"
                            :isBeingEdited="commentBeingEdited?.comment?.id === comment.id"
                            @delete="deleteComment"
                            @edit="editComment"
                            @cancelEdit="cancelEdit"
                            @saveEdit="saveEdit"
                            @updatePreview="updateCommentPreview"
                        />
                    </li>
                </ul>

                <Pagination :meta="props.comments.meta" :preserveScroll="true" :only="['comments']" />
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Comment from '@/Components/Comment.vue';
import Container from '@/Components/Container.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import { formatDate } from '@/Components/Utilities/date';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps(['post', 'comments']);

const comments = computed(() =>
    props.comments.data.map(comment =>
        commentBeingEdited.value?.comment?.id === comment.id
            ? { ...comment, body: commentBeingEdited.value.previewBody }
            : comment
    )
);

const postDateFormatted = computed(() => formatDate(props.post.created_at));

const commentForm = useForm({
    'body': '',
});

const addComment = () => {
    commentForm.post(
        route('posts.comments.store', props.post.id),
        {
            preserveScroll: true,
            onSuccess: () => commentForm.body = '',
        },
    );
};

const deleteComment = (commentId) =>
    router.delete(route('comments.destroy', { comment: commentId, page: props.comments.meta.current_page }), {
        preserveScroll: true,
    });

// Use a single ref for editing state
const commentBeingEdited = ref(null);

// Called when Comment emits 'edit'
const editComment = (comment) => {
    commentBeingEdited.value = {
        comment,
        previewBody: comment.body,
    };
};

const cancelEdit = () => {
    commentBeingEdited.value = null;
};

const updateCommentPreview = (value) => {
    if (commentBeingEdited.value) {
        commentBeingEdited.value.previewBody = value;
    }
};

const saveEdit = () => {
    if (!commentBeingEdited.value?.comment?.id) return;

    router.put(
        route('comments.update', commentBeingEdited.value.comment.id),
        { body: commentBeingEdited.value.previewBody, page: props.comments.meta.current_page },
        {
            preserveScroll: true,
            onSuccess: () => {
                commentBeingEdited.value = null;
            },
        }
    );
};
</script>
