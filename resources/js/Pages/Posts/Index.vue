<script setup>
import Container from '@/Components/Container.vue';
import PageHeading from '@/Components/PageHeading.vue';
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { formatDistance, parseISO } from 'date-fns';

defineOptions({
    layout: AppLayout,
});

const formattedDate = (date) => {
    return formatDistance(parseISO(date), new Date());
};

defineProps(['posts', 'selectedTopic']);
</script>

<template>
    <Container>
        <Link :href="route('posts.index')" class="text-indigo-500 hover:text-indigo-700 block mb-2">
            Back to all Posts
        </Link>
        <PageHeading v-text="selectedTopic ? selectedTopic.name : 'All posts'" />
        <p v-if="selectedTopic" class="mt-1 text-gray-600 text-sm">{{ selectedTopic.description }}</p>
        <ul class="divide-y mt-4">
            <li v-for="post in posts.data" class="px-2 py-4 flex justify-between items-baseline flex-col md:flex-row">
                <Link :href="post.routes.show" class="group block">
                        <span class="font-bold text-lg group-hover:text-blue-500 group-focus:text-blue-500">{{ post.title }}</span>
                        <span class="text-sm text-gray-600 block mt-1 group-hover:text-blue-500 group-focus:text-blue-500">{{ formattedDate(post.created_at) }} ago by {{ post.user?.name }}</span>
                </Link>
                <Link :href="route('posts.index', { topic: post.topic.slug })" class="rounded-full py-0.5 px-2 border border-pink-500 text-pink-500 hover:bg-indigo-500 hover:text-indigo-50">
                    {{ post.topic.name }}
                </Link>
            </li>
        </ul>

        <Pagination :meta="posts.meta"/>
    </Container>
</template>
