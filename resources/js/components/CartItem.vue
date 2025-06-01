<template>
    <div class="flex items-center justify-between border-b py-4 grid grid-cols-subgrid col-span-4">
        <div class="flex items-center">
            <img :src="item.image_url" :alt="item.name" class="w-16 h-16 object-cover rounded mr-4">
            <div>
                <h4 class="font-semibold text-gray-800">{{ item.name }}</h4>
                <p class="text-sm text-gray-600">{{ item.price }}€ / tk</p> </div>
        </div>
        <div class="flex items-center">
            <label :for="'cart-quantity-' + item.id" class="sr-only">Kogus</label>
            <input
                type="number"
                :id="'cart-quantity-' + item.id"
                :value="item.quantity"
                @input="updateQuantity($event.target.value)"
                min="1"
                class="w-16 border border-gray-300 rounded-md shadow-sm p-1 text-center text-gray-800"
            >
        </div>

            <div class="w-20 text-right font-semibold text-gray-800">{{ (item.price * item.quantity).toFixed(2) }}€</div>
            <button @click="removeFromCart" class="ml-4 text-red-500 hover:text-red-700 font-semibold">Eemalda</button>
    </div>
</template>

<script setup lang="ts">
import { cart } from '@/stores/cart';

const props = defineProps({
    item: Object,
});

const updateQuantity = (newQuantity) => {
    const quantityNum = parseInt(newQuantity);
    if (!isNaN(quantityNum) && quantityNum > 0) {
        cart.updateQuantity(props.item.id, quantityNum);
    } else if (quantityNum <= 0) {
        cart.updateQuantity(props.item.id, 1); // Or remove if 0, for this keep 1
    }
};

const removeFromCart = () => {
    cart.removeItem(props.item.id);
};
</script>
