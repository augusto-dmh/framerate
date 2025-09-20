<template>
    <div class="bg-white border-gray-300 rounded-md max-w-none focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full">
        <EditorContent :editor="editor"></EditorContent>
    </div>
</template>

<script setup>
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { Markdown } from 'tiptap-markdown';
import { watch } from 'vue';

const props = defineProps({
    modelValue: '',
});

const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    extensions: [
        StarterKit,
        Markdown,
    ],
    editorProps: {
        attributes: {
            class: 'prose prose-sm max-w-none py-1.5 px-3 min-h-[512px]',
        },
    },
    onUpdate: () => emit('update:modelValue', editor.value?.storage.markdown.getMarkdown()),
});

watch(() => props.modelValue, (value) => {
    if (value === editor.value?.storage.markdown.getMarkdown()) {
        return;
    }

    editor.value?.commands.setContent(value);
}), { immediate: true };
</script>
