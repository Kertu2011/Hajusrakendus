<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
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
const searchQuery = ref('');
const petsCurrentPage = ref(1);
const monstersCurrentPage = ref(1);
const itemsPerPage = ref(6);

const fetchPets = async () => {
    try {
        const response = await axios.get('/api/pets');
        pets.value = response.data;
    } catch (err) {
        if (err.response && err.response.status === 401) {
            window.location.href = '/login';
        } else {
            error.value = 'Failed to load pets. Please try again later.';
            console.error('Error fetching pets:', err);
        }
    } finally {
        loading.value = false;
    }
};

const fetchMonsters = async () => {
    try {
        const response = await axios.get('https://hajusrakendused.tak22parnoja.itmajakas.ee/current/public/index.php/api/monsters');
        monsters.value = response.data;
    } catch (err) {
        if (err.response && err.response.status === 401) {
            window.location.href = '/login';
        } else {
            error.value = 'Failed to load monsters. Please try again later.';
            console.error('Error fetching monsters:', err);
        }
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchPets();
    fetchMonsters();
});

const filteredPets = computed(() => {
    const filtered = pets.value.filter(pet =>
        pet.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
    const start = (petsCurrentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filtered.slice(start, end);
});

const totalPetPages = computed(() => {
    return Math.ceil(pets.value.filter(pet =>
        pet.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    ).length / itemsPerPage.value);
});

const nextPage = (type: 'pets' | 'monsters') => {
    if (type === 'pets' && petsCurrentPage.value < totalPetPages.value) {
        petsCurrentPage.value++;
    } else if (type === 'monsters' && monstersCurrentPage.value < totalMonsterPages.value) {
        monstersCurrentPage.value++;
    }
};

const prevPage = (type: 'pets' | 'monsters') => {
    if (type === 'pets' && petsCurrentPage.value > 1) {
        petsCurrentPage.value--;
    } else if (type === 'monsters' && monstersCurrentPage.value > 1) {
        monstersCurrentPage.value--;
    }
};

const filteredMonsters = computed(() => {
    const filtered = monsters.value.filter(monster =>
        monster.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
    const start = (monstersCurrentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filtered.slice(start, end);
});

const totalMonsterPages = computed(() => {
    return Math.ceil(monsters.value.filter(monster =>
        monster.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    ).length / itemsPerPage.value);
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

        <div class="mb-4">
            <input
                type="text"
                v-model="searchQuery"
                placeholder="Search by name..."
                class="w-full px-4 py-2 border rounded-lg"
            />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="pet in filteredPets" :key="pet.id" class="rounded-lg bg-white p-4 shadow-md text-black">
                <img :src="pet.image" alt="Pet" class="mb-4 h-48 w-full rounded-t-lg object-cover" />
                <h2 class="text-xl font-semibold">{{ pet.title }}</h2>
                <p class="text-gray-600">{{ pet.description }}</p>
                <p class="text-sm text-gray-500">Species: {{ pet.species }}</p>
                <p class="text-sm text-gray-500">Gender: {{ pet.gender }}</p>
                <p class="text-sm text-gray-500">Approximate Age: {{ pet.approximate_age || 'Unknown' }}</p>
            </div>
        </div>

        <div class="mt-4 flex justify-center items-center">
            <button @click="prevPage('pets')" :disabled="petsCurrentPage === 1" class="bg-gray-300 text-gray-700 px-4 py-2 rounded disabled:opacity-50">
                Previous
            </button>
            <span class="mx-4">Page {{ petsCurrentPage }} of {{ totalPetPages }}</span>
            <button @click="nextPage('pets')" :disabled="petsCurrentPage === totalPetPages" class="bg-gray-300 text-gray-700 px-4 py-2 rounded disabled:opacity-50">
                Next
            </button>
        </div>

        <div v-if="loading" class="text-center text-gray-500">Loading pets...</div>
        <div v-if="error" class="text-center text-red-500">{{ error }}</div>
        <div v-if="!loading && filteredPets.length === 0" class="text-center text-gray-500">No pets found.</div>
    </div>

    <div class="container mx-auto p-4 mt-8">
        <h1 class="text-3xl font-bold mb-4">Monsters</h1>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="monster in filteredMonsters" :key="monster.id" class="rounded-lg bg-white p-4 shadow-md text-black">
                <img v-if="monster.image" :src="monster.image" alt="Monster" class="mb-4 h-48 w-full rounded-t-lg object-cover" />
                <h2 class="text-xl font-semibold">{{ monster.title }}</h2>
                <p class="text-gray-600">{{ monster.description }}</p>
                <p class="text-sm text-gray-500">Habitat: {{ monster.habitat }}</p>
                <p class="text-sm text-gray-500">Behaviour: {{ monster.behaviour }}</p>
            </div>
        </div>

        <div class="mt-4 flex justify-center items-center">
            <button @click="prevPage('monsters')" :disabled="monstersCurrentPage === 1" class="bg-gray-300 text-gray-700 px-4 py-2 rounded disabled:opacity-50">
                Previous
            </button>
            <span class="mx-4">Page {{ monstersCurrentPage }} of {{ totalMonsterPages }}</span>
            <button @click="nextPage('monsters')" :disabled="monstersCurrentPage === totalMonsterPages" class="bg-gray-300 text-gray-700 px-4 py-2 rounded disabled:opacity-50">
                Next
            </button>
        </div>

        <div v-if="loading" class="text-center text-gray-500">Loading monsters...</div>
        <div v-if="error" class="text-center text-red-500">{{ error }}</div>
        <div v-if="!loading && filteredMonsters.length === 0" class="text-center text-gray-500">No monsters found.</div>
    </div>
</template>
