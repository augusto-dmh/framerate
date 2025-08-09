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
                    <li v-for="comment in comments.data" class="flex gap-4 px-2 py-4">
                        <Comment :comment="comment" />
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
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import { formatDate } from '@/Components/Utilities/date';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps(['post', 'comments']);

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

</script>
