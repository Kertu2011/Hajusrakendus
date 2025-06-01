// resources/js/Components/ProductCard.vue
<template>
    <div v-if="product && typeof product === 'object'" class="border rounded-lg p-4 shadow-lg flex flex-col justify-between">
        <div>
            <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-48 object-cover rounded mb-4">
            <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500 rounded mb-4">Pilt puudub</div>

            <h3 class="text-lg font-semibold mb-2">{{ product.name || 'Nimetu toode' }}</h3>
            <p class="text-sm text-gray-600 mb-2 h-20 overflow-y-auto">{{ product.description || 'Kirjeldus puudub' }}</p>
            <p class="text-xl font-bold text-blue-600 mb-3">{{ product.price != null ? product.price + '€' : 'Hind puudub' }}</p>
        </div>
        <div class="mt-auto">
            <div class="flex items-center mb-3">
                <label :for="'quantity-' + (product.id || 'temp')" class="mr-2 text-sm">Kogus:</label>
                <input
                    type="number"
                    :id="'quantity-' + (product.id || 'temp')"
                    v-model.number="quantity"
                    min="1"
                    class="w-20 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2"
                >
            </div>
            <button
                @click="addToCartHandler"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out"
                :class="{ 'bg-green-500 hover:bg-green-600': added }"
            >
                {{ added ? 'Lisatud!' : 'Lisa ostukorvi' }}
            </button>
        </div>
    </div>
    <div v-else class="border rounded-lg p-4 bg-red-100 text-red-700">
        <p>Viga: Tootekaardi andmed on vigased või puudulikud.</p>
        <pre>Saadud 'product' prop: {{ JSON.stringify(product) }}</pre>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { cart } from '@/stores/cart.js';

const props = defineProps({
    product: [Object, undefined], // Allow undefined temporarily for debugging
});

const quantity = ref(1);
const added = ref(false);

// It's good practice to also check if product exists before using it in methods
const addToCartHandler = () => {
    if (props.product && typeof props.product === 'object') {
        cart.addItem(props.product, quantity.value);
        added.value = true;
        setTimeout(() => {
            added.value = false;
        }, 1500);
    } else {
        console.error("Cannot add to cart: product data is invalid.", props.product);
    }
};

// Log if product is problematic
if (!props.product || typeof props.product !== 'object') {
    console.warn('ProductCard received an invalid product prop:', props.product);
}
</script>
