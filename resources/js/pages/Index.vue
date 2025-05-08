<template>
    <div class="bg-gray-100 dark:bg-gray-900 text-black dark:text-white min-h-screen flex flex-col items-center justify-center antialiased font-sans">
      <h1 class="text-4xl md:text-5xl font-bold mb-8 text-center px-4">
        Hajusrakenduste projektid
      </h1>
  
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-6 px-6 max-w-4xl w-full">
        <Link href="/weather" class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 text-center block">
          Ilmarakendus
        </Link>
  
        <Link href="/map" class="bg-purple-500 hover:bg-purple-600 dark:bg-purple-700 dark:hover:bg-purple-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 text-center block">
          Kaart
        </Link>
  
        <Link href="/blog" class="bg-green-500 hover:bg-green-600 dark:bg-green-700 dark:hover:bg-green-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 text-center block">
          Blogi ja kommentaarid (TODO)
        </Link>

        <Link href="/store" class="bg-pink-500 hover:bg-pink-600 dark:bg-pink-700 dark:hover:bg-pink-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 text-center block">
          E-pood (TODO)
        </Link>

        <Link href="/favourite-subject" class="bg-red-500 hover:bg-red-600 dark:bg-red-700 dark:hover:bg-red-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 text-center block">
          Lemmikteema (TODO)
        </Link>
      </div>
  
      <footer class="mt-12 text-center text-sm text-gray-500 dark:text-gray-400">
        &copy; Hajusrakendus
      </footer>
    </div>
  </template>

  <script setup lang="ts">
import { Link } from '@inertiajs/vue3'
</script>
  
