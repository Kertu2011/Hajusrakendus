<template>
    <div class="container mx-auto p-4 text-center max-w-xl">
        <Head title="Makse Õnnestus" />
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-6 rounded-lg shadow-md mt-10">
            <h1 class="text-3xl font-bold mb-4">🎉 Makse Õnnestus!</h1>
            <p class="text-lg mb-6">Teie tellimus on esitatud.</p> <Link href="/products" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Tagasi poodi
        </Link>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { cart } from '@/stores/cart.js';

onMounted(() => {
    // Clear the cart on successful payment
    cart.clearCart();
});
</script>
