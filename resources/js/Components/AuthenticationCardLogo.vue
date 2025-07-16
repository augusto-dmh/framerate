<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" fill="none" class="size-16">
            <circle cx="24" cy="24" r="12" fill="#4F46E5"/>
            <ellipse cx="24" cy="24" rx="20" ry="6" fill="none" stroke="#4F46E5" stroke-width="2" transform="rotate(-25 24 24)" />
        </svg>
    </Link>
</template>
