<template>
    <div :class="['mx-auto max-w-7xl sm:px-6 lg:px-8', pyClass]">
        <slot></slot>
    </div>
</template>

<script setup>
const props = defineProps({
    py: { type: String, default: 'py-10' }
});
const pyClass = props.py;
</script>
