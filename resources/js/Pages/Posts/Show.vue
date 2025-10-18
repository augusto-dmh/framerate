<template>
    <Head>
        <link rel="canonical" :href="post.routes.show" />
    </Head>

    <AppLayout :title="post.title">
        <Container>
            <Pill :href="route('posts.index', {topic: post.topic.slug})">{{ post.topic.name }}</Pill>
            <PageHeading class="mt-2">{{ post.title }}</PageHeading>
            <p class="mb-6 text-sm text-gray-500">{{ postDateFormatted }} by {{ post.user.name }}</p>

            <div class="mt-4">
                <span class="text-pink-500 font-bold">{{ post.likes_count }} likes</span>

                <div v-if="$page.props.auth.user" class="mt-2">
                    <Link v-if="post.can.like" :href="route('likes.store', ['post', post.id])" method="post" class="inline-block bg-indigo-600 hover:bg-pink-500 transition-colors text-white py-1.5 px-3 rounded-full">
                        <HandThumbUpIcon class="size-4 inline-block mr-1" />
                        Like Post
                    </Link>
                    <Link v-else :href="route('likes.destroy', ['post', post.id])" method="delete" class="inline-block bg-indigo-600 hover:bg-pink-500 transition-colors text-white py-1.5 px-3 rounded-full">
                        <HandThumbDownIcon class="size-4 inline-block mr-1" />
                        Unlike Post
                    </Link>
                </div>
            </div>

            <article class="mt-6 prose prose-sm max-w-none" v-html="post.html">
            </article>

            <div class="mt-12">
                <h2 class="text-xl font-semibold">Comments</h2>

                <form v-if="$page.props.auth.user" @submit.prevent="() => commentIdBeingEdited ? updateComment() : addComment()">
                    <div>
                        <InputLabel for="body" class="sr-only">Comment</InputLabel>
                        <MarkdownEditor ref="commentTextAreaRef" id="body" v-model="commentForm.body" editorClass="!min-h-[160px]" />
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
import PageHeading from '@/Components/PageHeading.vue';
import Pagination from '@/Components/Pagination.vue';
import Pill from '@/Components/Pill.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { HandThumbUpIcon, HandThumbDownIcon } from '@heroicons/vue/20/solid/index.js';
import { useConfirm } from '@/Components/Utilities/Composables/useConfirm';
import { formatDate } from '@/Components/Utilities/date';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, useForm, Head, Link } from '@inertiajs/vue3';
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

    router.delete(route('comments.destroy', {
        comment: commentId,
        page: props.comments.data.length > 1 ?
            props.comments.meta.current_page :
            Math.max(props.comments.meta.current_page - 1, 1),
    }), {
        preserveScroll: true,
    });
}
</script>
