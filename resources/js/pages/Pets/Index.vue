<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';

type Pet = {
    id: number;
    title: string;
    image: string;
    description: string;
    species: string;
    gender: string;
    created_at: string;
    updated_at: string;
    approximate_age?: string | null;
};

type Monster = {
    id: number;
    title: string;
    image: string;
    description: string;
    habitat: string;
    behaviour: string;
};

const pets = ref<Pet[]>([]);
const monsters = ref<Monster[]>([]);
const loading = ref(true);
const error = ref(null);

const fetchPets = async () => {
    try {
        const response = await axios.get('/api/pets');
        pets.value = response.data;
    } catch (error) {
        if (error.response.status === 401) {
            window.location.href = '/login';
        }

        error.value = 'Failed to load pets. Please try again later.';
        console.error('Error fetching pets:', error);
    } finally {
        loading.value = false;
    }
};

const fetchMonsters = async () => {
    try {
        const response = await axios.get('https://hajusrakendused.tak22parnoja.itmajakas.ee/current/public/index.php/api/monsters');
        monsters.value = response.data;
    } catch (error) {
        if (error.response.status === 401) {
            window.location.href = '/login';
        }

        error.value = 'Failed to load monsters. Please try again later.';
        console.error('Error fetching monsters:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchPets();
    fetchMonsters();
});
</script>

<template>
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold">Pets</h1>
            <Link href="/pets/create" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Add New Pet
            </Link>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="pet in pets" :key="pet.id" class="rounded-lg bg-white p-4 shadow-md text-black">
                <img :src="pet.image" alt="Pet" class="mb-4 h-48 w-full rounded-t-lg object-cover" />
                <h2 class="text-xl font-semibold">{{ pet.title }}</h2>
                <p class="text-gray-600">{{ pet.description }}</p>
                <p class="text-sm text-gray-500">Species: {{ pet.species }}</p>
                <p class="text-sm text-gray-500">Gender: {{ pet.gender }}</p>
                <p class="text-sm text-gray-500">Approximate Age: {{ pet.approximate_age || 'Unknown' }}</p>
            </div>
        </div>
        <div v-if="loading" class="text-center text-gray-500">Loading pets...</div>
        <div v-if="error" class="text-center text-red-500">{{ error }}</div>
        <div v-if="!loading && pets.length === 0" class="text-center text-gray-500">No pets available.</div>
    </div>
    <div class="container mx-auto p-4 mt-8">
        <h1 class="text-3xl font-bold mb-4">Monsters</h1>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="monster in monsters" :key="monster.id" class="rounded-lg bg-white p-4 shadow-md text-black">
                <img v-if="monster.image" :src="monster.image" alt="Monster" class="mb-4 h-48 w-full rounded-t-lg object-cover" />
                <h2 class="text-xl font-semibold">{{ monster.title }}</h2>
                <p class="text-gray-600">{{ monster.description }}</p>
                <p class="text-sm text-gray-500">Habitat: {{ monster.habitat }}</p>
                <p class="text-sm text-gray-500">Behaviour: {{ monster.behaviour }}</p>
            </div>
        </div>
        <div v-if="loading" class="text-center text-gray-500">Loading monsters...</div>
        <div v-if="error" class="text-center text-red-500">{{ error }}</div>
        <div v-if="!loading && monsters.length === 0" class="text-center text-gray-500">No monsters available.</div>
    </div>
</template>
