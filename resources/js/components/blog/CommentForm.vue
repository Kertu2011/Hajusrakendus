<template>
  <form @submit.prevent="submitComment" class="mt-4">
    <div class="mb-3">
      <label for="commentContent" class="block text-gray-700 text-sm font-bold mb-2">Add a comment:</label>
      <textarea
        id="commentContent"
        v-model="form.content"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-24"
        placeholder="Write your comment..."
        required
      ></textarea>
      <p v-if="form.errors.content" class="text-red-500 text-xs italic mt-2">{{ form.errors.content }}</p>
    </div>
    <button
      type="submit"
      class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
      :disabled="form.processing"
    >
      {{ form.processing ? 'Submitting...' : 'Post Comment' }}
    </button>
  </form>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  postId: {
    type: [String, Number],
    required: true,
  },
});

const emit = defineEmits(['comment-added']);

// Use Inertia's useForm for handling the comment submission
const form = useForm({
  content: '',
});

const submitComment = () => {
  form.post(route('blog.posts.comments.store', props.postId));
};
</script>