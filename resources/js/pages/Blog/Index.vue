<template>
  <div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Blog Posts</h1>
      <Link
        href="/blog/create/"
        class="bg-blue-500 hover:bg-blue-700 font-bold py-2 px-4 rounded"
      >
        Create Post
    </Link>
    </div>

    <div v-if="posts.length === 0" class="text-center">
      No posts yet. Be the first to create one!
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-black">
      <div
        v-for="post in posts"
        :key="post.id"
        class="bg-white shadow-lg rounded-lg p-6"
      >
        <h2 class="text-2xl font-semibold mb-2">{{ post.title }}</h2>
        <p class="mb-1">
          By: {{ post.user?.name || 'Unknown Author' }}
        </p>
        <p class="text-sm mb-4">
          {{ new Date(post.created_at).toLocaleDateString() }}
        </p>
        <p class="mb-4 truncate">
          {{ post.description.substring(0, 100) }}...
        </p>
        <Link
          :href="`/blog/posts/${post.id}`"
          class="text-blue-500 hover:text-blue-700 font-semibold"
        >
          Read More &raquo;
      </Link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { User, type SharedData } from '@/types';

type Post = {
  id: number;
  title: string;
  description: string;
  created_at: string;
  updated_at?: string;
  user?: Partial<User>;
};

const page = usePage<SharedData>();

const posts: Post[] = page.props.posts as Post[] || [];
</script>