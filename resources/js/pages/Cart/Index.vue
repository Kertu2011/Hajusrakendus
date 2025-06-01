<template>
    <div class="container mx-auto p-4">
        <Head title="Ostukorv" />
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-100">Sinu Ostukorv</h1>
        </header>

        <div v-if="cart.items.length === 0" class="text-center py-10">
            <p class="text-xl text-gray-500 mb-4">Ostukorv on tühi.</p> <Link href="/products" class="text-blue-500 hover:text-blue-700 font-semibold">
            Jätka ostlemist
        </Link>
        </div>

        <div v-else>
            <div class="bg-white shadow-md rounded-lg p-6 grid grid-cols-[1fr, 50px, 50px, 50px] gap-4 text-gray-800">
                <div>Toode</div>
                <div>Kogus</div>
                <div>Hind kokku</div>
                <div></div>

                <CartItem
                    v-for="item in cart.items"
                    :key="item.id"
                    :item="item"
                />

                <div class="mt-6 pt-6 border-t col-span-4">
                    <div class="flex justify-end items-center">
                        <span class="text-lg font-semibold text-gray-700 mr-4">Kogusumma:</span> <span class="text-2xl font-bold text-gray-900">{{ cart.totalPrice }}€</span>
                    </div>
                    <div class="mt-8 flex justify-between items-center">
                        <Link href="/products" class="text-blue-500 hover:text-blue-700 font-semibold">
                            &larr; Jätka ostlemist
                        </Link>
                        <Link
                            href="/checkout"
                            as="button"
                            :disabled="cart.items.length === 0"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded text-lg transition duration-150 ease-in-out"
                            :class="{ 'opacity-50 cursor-not-allowed': cart.items.length === 0 }"
                        >
                            Vormista ost </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { cart } from '@/stores/cart';
import CartItem from '@/components/CartItem.vue';
</script>
