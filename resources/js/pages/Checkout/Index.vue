<template>
    <div class="container mx-auto p-4 max-w-2xl">
        <Head title="Kassa" />
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-100 text-center">Vormista Ost</h1>
        </header>

        <div v-if="cart.items.length === 0 && !formSubmittedSuccessfully" class="text-center py-10">
            <p class="text-xl text-gray-500 mb-4">Sinu ostukorv on tühi. Lisa tooteid, et jätkata.</p>
            <Link href="/products" class="text-blue-500 hover:text-blue-700 font-semibold">
                Mine toodete lehele
            </Link>
        </div>

        <div v-else-if="!formSubmittedSuccessfully" class="bg-white shadow-md rounded-lg p-8 text-gray-800">
            <form @submit.prevent="submitCheckout">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Kontaktandmed</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">Eesnimi</label>
                            <input type="text" id="firstName" v-model="formData.firstName" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Perenimi</label>
                            <input type="text" id="lastName" v-model="formData.lastName" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" v-model="formData.email" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    </div>
                    <div class="mt-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                        <input type="tel" id="phone" v-model="formData.phone" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    </div>
                </div>

                <div v-if="Object.keys($page.props.errors).length > 0" class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <p v-for="(error, key) in $page.props.errors" :key="key">{{ error }}</p>
                </div>


                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-2">Tellimuse Kokkuvõte</h3>
                    <ul class="mb-4">
                        <li v-for="item in cart.items" :key="item.id" class="flex justify-between py-1 text-sm">
                            <span>{{ item.name }} x {{ item.quantity }}</span>
                            <span>{{ (item.price * item.quantity).toFixed(2) }}€</span>
                        </li>
                    </ul>
                    <div class="flex justify-between text-xl font-bold">
                        <span>Kogusumma:</span>
                        <span>{{ cart.totalPrice }}€</span>
                    </div>
                </div>


                <div class="mt-8 text-right">
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded text-lg transition duration-150 ease-in-out"
                        :disabled="isSubmitting"
                        :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }"
                    >
                        {{ isSubmitting ? 'Töötlen...' : 'Maksa' }} </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { cart } from '@/stores/cart.js';

const page = usePage();

const formData = reactive({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
});

const isSubmitting = ref(false);
const formSubmittedSuccessfully = ref(false); // To hide form after successful submission to next step

const submitCheckout = () => {
    // Basic client-side check (though HTML5 'required' does most of it)
    if (!formData.firstName || !formData.lastName || !formData.email || !formData.phone) {
        alert('Palun täida kõik kohustuslikud väljad.');
        return;
    }
    if (cart.items.length === 0) {
        alert('Ostukorv on tühi.');
        return;
    }

    isSubmitting.value = true;

    const dataToSubmit = {
        user_info: { ...formData },
        cart_items: cart.items.map(item => ({ id: item.id, name: item.name, price: item.price, quantity: item.quantity })), // Send necessary cart item details
        total_price: cart.totalPrice,
    };

    router.post('/checkout/initiate-payment', dataToSubmit, {
        onSuccess: () => {
            formSubmittedSuccessfully.value = true;
            // Page will navigate to MockPaymentConfirmation via controller
        },
        onError: (errors) => {
            console.error('Checkout errors:', errors);
            // Errors are automatically available in $page.props.errors
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};

// Redirect to products if cart is empty and not already submitting
onMounted(() => {
    if (cart.items.length === 0) {
        router.visit('/products');
    }
});
</script>
