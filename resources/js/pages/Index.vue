<template>
    <div class="bg-gray-100 dark:bg-gray-900 text-black dark:text-white min-h-screen flex flex-col items-center justify-center antialiased font-sans">
      <h1 class="text-4xl md:text-5xl font-bold mb-8 text-center px-4">
        Welcome to Your Project Hub!
      </h1>
  
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 px-6 max-w-4xl w-full">
        <Link href="/weather" class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 text-center block">
          Project One
        </Link>
  
        <Link href="/project-two" class="bg-green-500 hover:bg-green-600 dark:bg-green-700 dark:hover:bg-green-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 text-center block">
          Project Two
        </Link>
  
        <Link href="/project-three" class="bg-purple-500 hover:bg-purple-600 dark:bg-purple-700 dark:hover:bg-purple-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 text-center block">
          Project Three
        </Link>
      </div>
  
      <footer class="mt-12 text-center text-sm text-gray-500 dark:text-gray-400">
        &copy; Your Application Name
      </footer>
    </div>
  </template>

  <script setup lang="ts">
import { Link } from '@inertiajs/vue3'
</script>
  
