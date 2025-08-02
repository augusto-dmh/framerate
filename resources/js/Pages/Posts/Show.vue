<template>
    <AppLayout :title="post.title">
        <Container>
            <h1 class="text-2xl font-bold mb-4">{{ post.title }}</h1>
            <p>{{ post.body }}</p>
            <p>Publicado: {{ postDateFormatted }}</p>

            <div class="mt-12 mt-4">
                <h2 class="text-xl font-semibold">Comments</h2>
                <ul class="divide-y">
                    <li v-for="comment in comments.data" class="flex gap-4 px-2 py-4">
                        <Comment :comment="comment" />
                    </li>
                </ul>

                <Pagination :meta="comments.meta" :preserveScroll="true"/>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>
import Comment from '@/Components/Comment.vue';
import Container from '@/Components/Container.vue';
import Pagination from '@/Components/Pagination.vue';
import { formatDate } from '@/Components/Utilities/date';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps(['post', 'comments']);

const postDateFormatted = computed(() => formatDate(props.post.created_at));
</script>
