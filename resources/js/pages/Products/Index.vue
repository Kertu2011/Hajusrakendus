<template>
    <div class="container mx-auto p-4">
        <div class="mb-6 flex flex-row items-center justify-between">
            <h1 class="text-2xl font-bold">E-pood</h1>
            <div>
                <Link
                    href="/cart"
                    class="flex items-center rounded bg-blue-500 px-4 py-2 font-bold text-white transition duration-150 ease-in-out hover:bg-blue-700"
                >
                    <ShoppingCartIcon v-if="cart.items.length > 0" class="mr-2 h-6 w-6" />
                    <EmptyCartIcon v-else class="mr-2 h-6 w-6" />
                    {{ cart.items.reduce((total, item) => total + item.quantity, 0) }}
                </Link>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div v-for="product in productsWithQuantities" :key="product.id" class="flex flex-col justify-between rounded-lg border p-4 shadow-lg bg-gray-800">
                <div>
                    <img :src="product.image_url" :alt="product.name" class="mb-4 h-48 w-full rounded object-cover" />
                    <h3 class="mb-2 text-lg font-semibold">{{ product.name }}</h3>
                    <p class="mb-2 h-20 overflow-y-auto text-sm text-gray-400">{{ product.description }}</p>
                    <p class="mb-3 text-xl font-bold text-blue-600">{{ product.price }}€</p>
                </div>
                <div class="mt-auto">
                    <div class="mb-3 flex items-center">
                        <div class="flex">
                            <button
                                @click="decrementQuantity(product.id)"
                                :disabled="product.currentQuantity <= 1"
                                class="rounded-l bg-gray-200 px-2 py-1 font-medium text-gray-700 transition duration-150 ease-in-out hover:bg-gray-300 focus:ring-2 focus:ring-blue-300 focus:outline-none disabled:opacity-50"
                            >
                                -
                            </button>
                            <div class="w-10 border-t border-b border-gray-200 bg-white py-1 text-center font-medium text-gray-700">
                                {{ product.currentQuantity }}
                            </div>
                            <button
                                @click="incrementQuantity(product.id)"
                                class="rounded-r bg-gray-200 px-2 py-1 font-medium text-gray-700 transition duration-150 ease-in-out hover:bg-gray-300 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                            >
                                +
                            </button>
                        </div>
                    </div>
                    <button
                        @click="addToCart(product)"
                        class="w-full rounded bg-blue-500 px-4 py-2 font-bold text-white transition duration-150 ease-in-out hover:bg-blue-700"
                        :class="{ 'bg-green-500 hover:bg-green-600': addedProducts[product.id] }"
                    >
                        {{ addedProducts[product.id] ? 'Lisatud!' : 'Lisa ostukorvi' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, reactive } from 'vue';
import { cart } from '@/stores/cart';
import { Link, usePage } from '@inertiajs/vue3';
import type { Product, SharedData } from '@/types';
import { ShoppingCartIcon } from '@heroicons/vue/24/solid';
import { ShoppingCartIcon as EmptyCartIcon } from '@heroicons/vue/24/outline';

interface ProductWithQuantity extends Product {
    currentQuantity: number;
}

const quantities = reactive<Record<number, number>>({});
const addedProducts = reactive<Record<number, boolean>>({});

const page = usePage<SharedData>();

const baseProducts = computed(() => (page.props.products as Product[]) || []);

const productsWithQuantities = computed<ProductWithQuantity[]>(() => {
    return baseProducts.value.map((product) => ({
        ...product,
        currentQuantity: quantities[product.id] === undefined ? 1 : quantities[product.id],
    }));
});

const incrementQuantity = (productId: number) => {
    const currentQuantity = quantities[productId] === undefined ? 1 : quantities[productId];
    quantities[productId] = currentQuantity + 1;
};

const decrementQuantity = (productId: number) => {
    const currentQuantity = quantities[productId] === undefined ? 1 : quantities[productId];
    if (currentQuantity > 1) {
        quantities[productId] = currentQuantity - 1;
        if (quantities[productId] === 1) {
            delete quantities[productId];
        }
    }
};

const addToCart = (product: ProductWithQuantity) => {
    cart.addItem(product, product.currentQuantity);
    addedProducts[product.id] = true;
    setTimeout(() => {
        addedProducts[product.id] = false;
    }, 1500);
};
</script>
