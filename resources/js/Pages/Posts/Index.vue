<script setup>
import Container from '@/Components/Container.vue';
import PageHeading from '@/Components/PageHeading.vue';
import Pagination from '@/Components/Pagination.vue';
import Pill from '@/Components/Pill.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { formatDate } from '@/Components/Utilities/date';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps(['posts', 'topics', 'selectedTopic', 'query']);

const searchForm = useForm({
    query: props.query,
    page: 1,
});

const page = usePage();
const search = () => searchForm.get(page.url);
const clearSearch = () => {
    searchForm.query = '';
    search();
};
</script>

<template>
    <Container>
        <PageHeading v-text="selectedTopic ? selectedTopic.name : 'All posts'" />
        <p v-if="selectedTopic" class="mt-1 text-gray-600 text-sm">{{ selectedTopic.description }}</p>
        <menu class="flex list-none space-x-1 mt-3 overflow-x-auto pb-2 pt-1">
            <li><Pill :filled="!selectedTopic" :href="route('posts.index', { query: searchForm.query })">All Posts</Pill></li>
            <li v-for="topic in topics" :key="topic.id">
                <Pill :filled="selectedTopic?.id === topic.id" :href="route('posts.index', { topic: topic.slug, query: searchForm.query })">
                    {{ topic.name }}
                </Pill>
            </li>
        </menu>

        <form @submit.prevent="search" class="mt-4">
            <div>
                <InputLabel for="query">Search</InputLabel>
                <div class="flex mt-1 space-x-2">
                    <TextInput v-model="searchForm.query" class="w-full" id="query" />
                    <SecondaryButton type="submit">Search</SecondaryButton>
                    <DangerButton v-if="searchForm.query" @click="clearSearch">Clear</DangerButton>
                </div>
            </div>
        </form>

        <ul class="divide-y mt-4">
            <li v-for="post in posts.data" class="px-2 py-4 flex justify-between items-baseline flex-col md:flex-row">
                <Link :href="post.routes.show" class="group block">
                        <span class="font-bold text-lg group-hover:text-blue-500 group-focus:text-blue-500">{{ post.title }}</span>
                        <span class="text-sm text-gray-600 block mt-1 group-hover:text-blue-500 group-focus:text-blue-500">{{ formatDate(post.created_at) }} by {{ post.user?.name }}</span>
                </Link>
                <Pill :href="route('posts.index', { topic: post.topic.slug })">
                    {{ post.topic.name }}
                </Pill>
            </li>
        </ul>

        <Pagination :meta="posts.meta"/>
    </Container>
</template>
