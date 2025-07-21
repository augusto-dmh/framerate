<script setup lang="ts">
import Container from '@/Components/Container.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout,
});

defineProps(['posts']);
</script>

<template>
    <Container>
        <ul class="divide-y">
            <li v-for="post in posts" class="px-2 py-4">
                <span class="font-bold text-lg">{{ post.title }}</span>
            </li>
        </ul>
    </Container>
</template>
