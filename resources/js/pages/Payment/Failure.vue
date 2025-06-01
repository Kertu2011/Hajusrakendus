<template>
    <div class="container mx-auto p-4 text-center max-w-xl">
        <Head title="Makse Ebaõnnestus" />
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-6 rounded-lg shadow-md mt-10">
            <h1 class="text-3xl font-bold mb-4">⚠️ Makse Tühistati või Ebaõnnestus</h1>
            <p class="text-lg mb-6">Teie tooted on endiselt ostukorvis.</p> <div class="space-x-4">
            <Link href="/cart" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                Vaata Ostukorvi
            </Link>
            <Link href="/checkout" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Proovi uuesti maksta
            </Link>
        </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
// Cart is not cleared here
</script>
