<template>
  <div class="container mx-auto p-4">
    <div v-if="error" class="text-red-500 text-center">{{ error }}</div>

    <div v-if="post" class="bg-white text-black shadow-lg rounded-lg p-6">
      <h1 class="text-4xl font-bold mb-3">{{ post.title }}</h1>
      <p class="text-gray-600 text-sm mb-1">
        By: {{ post.user?.name || 'Unknown Author' }}
      </p>
      <p class="text-gray-500 text-sm mb-6">
        Posted on: {{ new Date(post.created_at).toLocaleDateString() }}
      </p>
      <div class="prose max-w-none mb-8" v-html="post.description"></div>
      <div v-if="currentUser" class="mt-4 mb-6">
        <Link
          v-if="currentUser.id === post.user_id || currentUser.is_admin"
          :href="route('blog.posts.edit', post.id)"
          class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2"
        >
          Edit Post
        </Link>
        <button
          v-if="currentUser.id === post.user_id || currentUser.is_admin"
          @click="deletePost"
          class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
          :disabled="deletingPost"
        >
          {{ deletingPost ? 'Deleting...' : 'Delete Post' }}
        </button>
      </div>


      <hr class="my-8" />

      <h3 class="text-2xl font-semibold mb-4">Comments</h3>
      <div v-if="post.comments && post.comments.length > 0">
        <div
          v-for="comment in post.comments"
          :key="comment.id"
          class="bg-gray-100 p-4 rounded-lg mb-4"
        >
          <p class="font-semibold">{{ comment.user?.name || 'Anonymous' }}
            <span class="text-gray-500 text-sm ml-2">
              {{ new Date(comment.created_at).toLocaleString() }}
            </span>
          </p>
          <p class="text-gray-700">{{ comment.content }}</p>
            <button
              v-if="currentUser && currentUser.is_admin"
              @click="deleteComment(comment.id)"
              class="text-red-500 hover:text-red-700 text-xs mt-1"
              :disabled="deletingComment === comment.id"
            >
              {{ deletingComment === comment.id ? 'Deleting...' : 'Delete Comment (Admin)' }}
            </button>
        </div>
      </div>
      <p v-else class="text-gray-500">No comments yet.</p>

      <CommentForm v-if="currentUser" :post-id="post.id" class="mt-6" />
        <p v-else class="mt-6 text-gray-600">
        Please <Link :href="route('login')" class="text-blue-500 hover:underline">login</Link> to comment.
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import CommentForm from '@/components/blog/CommentForm.vue';
import { type SharedData } from '@/types';

// Define Post and Comment types based on your backend structure
type Post = {
  id: number;
  title: string;
  description: string;
  created_at: string;
  updated_at?: string;
  user?: { id: number; name: string };
  comments?: Comment[];
  user_id: number; // Add user_id
};

type Comment = {
  id: number;
  user?: { name: string };
  created_at: string;
  content: string;
};

// Get the post and current user from page props
const page = usePage<SharedData & { post: Post; currentUser: { id: number; is_admin: boolean } | null }>();
const post = computed<Post>(() => page.props.post);
const currentUser = page.props.auth.user;

const error = ref<string | null>(null);
const deletingPost = ref(false);
const deletingComment = ref<number | null>(null);


const deletePost = async () => {
  if (!post) return;
  if (confirm('Are you sure you want to delete this post?')) {
    deletingPost.value = true;
    router.delete(route('blog.posts.destroy', post.value.id));
  }
};

const deleteComment = async (commentId: number) => {
  if (!currentUser.is_admin) {
    alert('Only admins can delete comments.');
    return;
  }
  if (confirm('Are you sure you want to delete this comment? (Admin Action)')) {
    deletingComment.value = commentId;
    router.delete(route('comments.destroy', commentId), {
      onSuccess: () => {
        if (post && post.value.comments) {
          post.value.comments = post.value.comments.filter(c => c.id !== commentId);
        }
        deletingComment.value = null;
      },
      onError: (err) => {
        console.error('Error deleting comment:', err);
        alert('Failed to delete comment. ' + (err.message || ''));
        deletingComment.value = null;
      },
    });
  }
};
</script>
