<script setup>
import Container from '@/Components/Container.vue';
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

defineProps(['posts']);
</script>

<template>
    <Container>
        <ul class="divide-y">
            <li v-for="post in posts.data" class="px-2 py-4">
                <Link :href="post.routes.show" class="group block">
                        <span class="font-bold text-lg group-hover:text-blue-500 group-focus:text-blue-500">{{ post.title }}</span>
                        <span class="text-sm text-gray-600 block mt-1 group-hover:text-blue-500 group-focus:text-blue-500">{{ formattedDate(post.created_at) }} ago by {{ post.user?.name }}</span>
                </Link>
            </li>
        </ul>

        <Pagination :meta="posts.meta"/>
    </Container>
</template>
