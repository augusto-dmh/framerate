<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const textArea = ref(null);

onMounted(() => {
    if (textArea.value.hasAttribute('autofocus')) {
        textArea.value.focus();
    }
});

defineExpose({ focus: () => textArea.value.focus() });
</script>

<template>
    <textarea
        ref="textArea"
        class="border-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
        rows="4"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>
</template>
