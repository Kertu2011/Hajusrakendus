<template>
    <div class="container mx-auto p-4 max-w-lg text-gray-800">
        <Head title="Kinnita Makse" />
        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-100">Makse Kinnitamine</h1>
        </header>

        <div class="bg-white shadow-md rounded-lg p-8">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">Tellimuse Detailid</h2>
                <div class="mb-4 p-4 border rounded bg-gray-50">
                    <p><strong>Nimi:</strong> {{ checkoutData.user_info.firstName }} {{ checkoutData.user_info.lastName }}</p>
                    <p><strong>Email:</strong> {{ checkoutData.user_info.email }}</p>
                    <p><strong>Telefon:</strong> {{ checkoutData.user_info.phone }}</p>
                </div>
                <h3 class="text-lg font-semibold mb-2">Tooted:</h3>
                <ul class="list-disc list-inside mb-2">
                    <li v-for="item in checkoutData.cart_items" :key="item.id">
                        {{ item.name }} ({{ item.quantity }} tk) - {{ (item.price * item.quantity).toFixed(2) }}€
                    </li>
                </ul>
                <p class="text-xl font-bold mt-4">Kogusumma: {{ parseFloat(checkoutData.total_price).toFixed(2) }}€</p>
            </div>

            <p class="text-center text-gray-600 mb-6">
                Palun kinnita või tühista makse.
            </p>

            <div class="flex justify-around mt-8 gap-2">
                <button
                    @click="processPayment('success')"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-8 rounded text-lg transition duration-150 ease-in-out"
                    :disabled="isProcessing"
                >
                    SOORITA MAKSE </button>
                <button
                    @click="processPayment('cancel')"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-8 rounded text-lg transition duration-150 ease-in-out"
                    :disabled="isProcessing"
                >
                    TÜHISTA MAKSE </button>
            </div>
            <p v-if="isProcessing" class="text-center mt-4 text-blue-600">Töötlen...</p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    checkoutData: Object, // Contains user_info and cart_items passed from CheckoutController
});

const isProcessing = ref(false);

const processPayment = (status) => {
    isProcessing.value = true;
    router.post('/checkout/process-payment', {
        status: status,
        user_info: props.checkoutData.user_info, // Pass through for potential logging or if controller needs it
        cart_items: props.checkoutData.cart_items // Pass through
    }, {
        onFinish: () => isProcessing.value = false,
    });
};
</script>
