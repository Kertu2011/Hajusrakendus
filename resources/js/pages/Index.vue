<template>

    <div class="bg-gray-100 dark:bg-gray-900 text-black dark:text-white min-h-screen flex flex-col items-center justify-center antialiased font-sans">
      <div class="flex space-x-4 mb-8">
        <template v-if="!user">
          <Link
            href="/login"
            class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-semibold"
          >
            Logi sisse
          </Link>
          <Link
            href="/register"
            class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-semibold"
          >
            Registreeri
          </Link>
        </template>
        <template v-else>
          <div class="flex items-center gap-4">
            <span class="text-gray-600 dark:text-gray-400">Tere, {{ user.name }}</span>
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-semibold"
              preserve-scroll
            >
              Logi välja
            </Link>
          </div>
        </template>
      </div>
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
          Blogi ja kommentaarid
        </Link>

        <Link href="/products" class="bg-pink-500 hover:bg-pink-600 dark:bg-pink-700 dark:hover:bg-pink-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 text-center block">
          E-pood
        </Link>

        <Link href="/pets" class="bg-red-500 hover:bg-red-600 dark:bg-red-700 dark:hover:bg-red-800 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 text-center block">
          Lemmikteema (Loomad)
        </Link>
      </div>

      <footer class="mt-12 text-center text-sm text-gray-500 dark:text-gray-400">
        &copy; Hajusrakendus
      </footer>
    </div>
  </template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios';

const { props } = usePage()
const { user } = props.auth || { user: null }

axios.get('/sanctum/csrf-cookie').then(() => {
    // CSRF cookie is set, you can now make authenticated requests
});

</script>

<style scoped>
</style>

