<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3'; // Import Inertia's router
import { Link } from '@inertiajs/vue3';

// Define the structure for form data
const form = ref({
    title: '',
    image: '', // URL for the image
    description: '',
    species: '',
    gender: 'unknown', // Default gender
    approximate_age: '', // e.g., "6 months", "2 years"
});

// For handling submission state and errors
const submitting = ref(false);
const errors = ref<{ [key: string]: string[] }>({}); // To store validation errors from backend
const successMessage = ref('');

const genderOptions = ['male', 'female', 'unknown'];

const handleSubmit = async () => {
    submitting.value = true;
    errors.value = {}; // Clear previous errors
    successMessage.value = '';

    try {
        const response = await axios.post('/api/pets', form.value);
        successMessage.value = response.data.message || 'Pet created successfully!';

        // Optionally, clear the form
        form.value = {
            title: '',
            image: '',
            description: '',
            species: '',
            gender: 'unknown',
            approximate_age: '',
        };

        // Redirect to the pets index page after a short delay to show success message
        setTimeout(() => {
            router.visit('/pets');
        }, 1500);

    } catch (error: any) {
        if (error.response) {
            if (error.response.status === 422) {
                // Laravel validation errors
                errors.value = error.response.data.errors;
            } else if (error.response.status === 401) {
                // Unauthorized, redirect to login
                window.location.href = '/login';
            } else {
                // Other server errors
                errors.value = { general: ['An unexpected error occurred. Please try again.'] };
                console.error('Error creating pet:', error.response.data);
            }
        } else {
            // Network errors or other issues
            errors.value = { general: ['Failed to connect to the server. Please check your network.'] };
            console.error('Error creating pet:', error.message);
        }
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <div class="container mx-auto p-4 text-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Add New Pet</h1>
            <Link href="/pets" class="text-blue-500 hover:text-blue-700 hover:underline">
                &laquo; Back to Pets
            </Link>
        </div>

        <div v-if="successMessage" class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded">
            {{ successMessage }}
        </div>

        <form @submit.prevent="handleSubmit" class="max-w-lg mx-auto bg-white p-6 shadow-md rounded-lg">
            <div v-if="errors.general" class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded">
                <p v-for="(error, index) in errors.general" :key="`general-error-${index}`">{{ error }}</p>
            </div>

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" id="title" v-model="form.title"
                       class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                       :class="{'border-red-500': errors.title}"
                       required>
                <div v-if="errors.title" class="text-red-500 text-xs mt-1">
                    <p v-for="(error, index) in errors.title" :key="`title-error-${index}`">{{ error }}</p>
                </div>
            </div>

            <div class="mb-4">
                <label for="species" class="block text-sm font-medium text-gray-700 mb-1">Species <span class="text-red-500">*</span></label>
                <input type="text" id="species" v-model="form.species"
                       class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                       :class="{'border-red-500': errors.species}"
                       required>
                <div v-if="errors.species" class="text-red-500 text-xs mt-1">
                    <p v-for="(error, index) in errors.species" :key="`species-error-${index}`">{{ error }}</p>
                </div>
            </div>

            <div class="mb-4">
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                <select id="gender" v-model="form.gender"
                        class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                        :class="{'border-red-500': errors.gender}"
                        required>
                    <option v-for="option in genderOptions" :key="option" :value="option">{{ option.charAt(0).toUpperCase() + option.slice(1) }}</option>
                </select>
                <div v-if="errors.gender" class="text-red-500 text-xs mt-1">
                    <p v-for="(error, index) in errors.gender" :key="`gender-error-${index}`">{{ error }}</p>
                </div>
            </div>

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image URL <span class="text-red-500">*</span></label>
                <input type="text" id="image" v-model="form.image" placeholder="https://example.com/image.jpg"
                       class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                       :class="{'border-red-500': errors.image}"
                       required>
                <div v-if="errors.image" class="text-red-500 text-xs mt-1">
                    <p v-for="(error, index) in errors.image" :key="`image-error-${index}`">{{ error }}</p>
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                <textarea id="description" v-model="form.description" rows="3"
                          class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                          :class="{'border-red-500': errors.description}" required></textarea>
                <div v-if="errors.description" class="text-red-500 text-xs mt-1">
                    <p v-for="(error, index) in errors.description" :key="`description-error-${index}`">{{ error }}</p>
                </div>
            </div>

            <div class="mb-6">
                <label for="approximate_age" class="block text-sm font-medium text-gray-700 mb-1">Approximate Age</label>
                <input type="text" id="approximate_age" v-model="form.approximate_age" placeholder="e.g., 6 months, 2 years"
                       class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                       :class="{'border-red-500': errors.approximate_age}">
                <p class="text-xs text-gray-500 mt-1">Format: number followed by 'months' or 'years' (e.g., "3 months", "5 years").</p>
                <div v-if="errors.approximate_age" class="text-red-500 text-xs mt-1">
                    <p v-for="(error, index) in errors.approximate_age" :key="`age-error-${index}`">{{ error }}</p>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit"
                        :disabled="submitting"
                        class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 disabled:opacity-50">
                    <span v-if="submitting">Submitting...</span>
                    <span v-else>Create Pet</span>
                </button>
            </div>
        </form>
    </div>
</template>
