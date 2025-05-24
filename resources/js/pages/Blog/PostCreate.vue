<template>
  <div class="container mx-auto p-4 max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">Create New Post</h1>
    <form @submit.prevent="submit" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
          Title
        </label>
        <input
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          id="title"
          type="text"
          v-model="form.title"
          placeholder="Post Title"
          required
        />
        <p v-if="form.errors.title" class="text-red-500 text-xs italic mt-2">{{ form.errors.title }}</p>
      </div>
      <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
          Description
        </label>
        <textarea
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline h-40"
          id="description"
          v-model="form.description"
          placeholder="Write your post content here..."
          required
        ></textarea>
        <p v-if="form.errors.description" class="text-red-500 text-xs italic mt-2">{{ form.errors.description }}</p>
      </div>
      <div class="flex items-center justify-between">
        <button
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          type="submit"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Updating...' : 'Update Post' }}
        </button>
        <Link href="/blog" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
            Cancel
        </Link>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  title: '',
  description: '',
});

const submit = () => {
  form.put(route('blog.posts'))
}
</script>