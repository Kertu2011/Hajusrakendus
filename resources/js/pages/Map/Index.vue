<template>
    <Head title="Map" />
    <div class="min-h-screen bg-slate-50">
      <div class="container mx-auto p-4 sm:p-6 md:p-8">
        <div class="flex justify-between items-center mb-8">
          <h1 class="text-3xl sm:text-4xl font-bold text-slate-800 tracking-tight">OpenStreetMap Markers</h1>
        </div>
    
        <div v-if="successMessage"
             class="flex items-center mb-6 p-4 bg-emerald-50 text-emerald-700 border border-emerald-300 rounded-lg shadow-md">
          <InformationCircleIcon class="h-6 w-6 mr-3 text-emerald-600 flex-shrink-0" />
          <span>{{ successMessage }}</span>
        </div>
    
        <div ref="mapContainer" style="height: 60vh; z-index: 1;" class="mb-8 border border-slate-300 rounded-xl shadow-xl overflow-hidden"></div>
    
        <!-- Modal for adding/editing marker -->
        <div v-if="showAddMarkerModal || showEditMarkerModal"
           class="fixed inset-0 bg-black/60 flex justify-center items-center p-4 overflow-y-auto"
           style="z-index: 1000;">
          <div class="relative bg-white p-6 sm:p-8 rounded-xl shadow-2xl w-full max-w-lg transform transition-all"
               style="z-index: 1001;"
               @click.stop> 
            <button @click="closeModal"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100"> 
              <XMarkIcon class="h-6 w-6" />
            </button>
            <h3 class="text-2xl font-semibold text-slate-800 mb-6 pb-4 border-b border-slate-200 flex items-center">
              <MapPinIcon v-if="!showEditMarkerModal" class="h-7 w-7 mr-2.5 text-sky-600" />
              <PencilSquareIcon v-if="showEditMarkerModal" class="h-7 w-7 mr-2.5 text-amber-500" />
              {{ showEditMarkerModal ? 'Edit Marker' : 'Add New Marker' }}
            </h3>
            <form @submit.prevent="submitMarker" class="space-y-5">
              <div>
                <label for="markerName" class="block text-sm font-medium text-slate-700 mb-1.5">Name (Optional)</label>
                <input type="text" id="markerName"
                       v-model="form.name"
                       placeholder="E.g., Home, Office, Favorite Park"
                       class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 sm:text-sm text-slate-900 placeholder-slate-400" />
                <p v-if="form.errors.name" class="text-xs text-red-500 mt-1.5">{{ form.errors.name }}</p>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="markerLat" class="block text-sm font-medium text-slate-700 mb-1.5">Latitude</label>
                  <input type="number" id="markerLat" step="any" v-model.number="form.latitude" readonly
                         class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-slate-100 text-slate-600 sm:text-sm cursor-not-allowed focus:ring-0 focus:border-slate-300" />
                  <p v-if="form.errors.latitude" class="text-xs text-red-500 mt-1.5">{{ form.errors.latitude }}</p>
                </div>
                <div>
                  <label for="markerLng" class="block text-sm font-medium text-slate-700 mb-1.5">Longitude</label>
                  <input type="number" id="markerLng" step="any" v-model.number="form.longitude" readonly
                         class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-lg bg-slate-100 text-slate-600 sm:text-sm cursor-not-allowed focus:ring-0 focus:border-slate-300" />
                  <p v-if="form.errors.longitude" class="text-xs text-red-500 mt-1.5">{{ form.errors.longitude }}</p>
                </div>
              </div>
              <div>
                <label for="markerDesc" class="block text-sm font-medium text-slate-700 mb-1.5">Description (Optional)</label>
                <textarea id="markerDesc" v-model="form.description" 
                          rows="4"
                          placeholder="Any details about this marker..."
                          class="mt-1 block w-full px-3.5 py-2.5 border border-slate-300 rounded-lg shadow-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 sm:text-sm text-slate-900 placeholder-slate-400">
                </textarea>
                <p v-if="form.errors.description" class="text-xs text-red-500 mt-1.5">{{ form.errors.description }}</p>
              </div>
              <div class="flex justify-end space-x-3 pt-6 mt-6 border-t border-slate-200">
                <button type="button" @click="closeModal"
                        class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-400 transition-colors">
                  Cancel
                </button>
                <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white border border-transparent rounded-lg shadow-sm disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                        :class="{
                          'bg-sky-600 hover:bg-sky-700 focus:ring-sky-500': !showEditMarkerModal,
                          'bg-amber-500 hover:bg-amber-600 focus:ring-amber-500': showEditMarkerModal
                        }">
                  <ArrowPathIcon v-if="form.processing" class="animate-spin h-5 w-5 mr-2 -ml-1" />
                  <CheckCircleIcon v-if="!form.processing && showEditMarkerModal" class="h-5 w-5 mr-2 -ml-1" />
                  <MapPinIcon v-if="!form.processing && !showEditMarkerModal" class="h-5 w-5 mr-2 -ml-1" />
                  <span>{{ form.processing ? 'Saving...' : (showEditMarkerModal ? 'Save Changes' : 'Add Marker') }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
  import { Head, useForm, router, usePage } from '@inertiajs/vue3';
  import L, { LatLng, Map, Marker as LeafletMarker, LeafletMouseEvent, PopupEvent } from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  
  import {
    XMarkIcon,
    InformationCircleIcon,
    MapPinIcon,
    PencilSquareIcon,
    TrashIcon,
    CheckCircleIcon,
    ArrowPathIcon
  } from '@heroicons/vue/24/outline';
  
  import iconUrl from 'leaflet/dist/images/marker-icon.png';
  import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
  import shadowUrl from 'leaflet/dist/images/marker-shadow.png';
  
  delete (L.Icon.Default.prototype as any)._getIconUrl;
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: iconRetinaUrl,
    iconUrl: iconUrl,
    shadowUrl: shadowUrl,
  });
  
  interface MarkerData {
    id: number;
    name: string | null;
    latitude: number;
    longitude: number;
    description: string | null;
    created_at: string;
    updated_at: string;
  }
  
  const props = defineProps<{
    markers: MarkerData[];
  }>();
  
  const page = usePage();
  
  const mapContainer = ref<HTMLElement | null>(null);
  let map: Map | null = null;
  const leafletMarkers = ref<LeafletMarker[]>([]);
  
  const showAddMarkerModal = ref(false);
  const showEditMarkerModal = ref(false);
  const currentEditingMarker = ref<MarkerData | null>(null);
  
  const form = useForm({
    id: null as number | null,
    name: '',
    latitude: 0,
    longitude: 0,
    description: '',
  });
  
  const pencilSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" /><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" /></svg>`;
  const trashSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25-.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>`;
  
  const openAddModal = (latlng: LatLng) => {
    form.reset();
    form.latitude = parseFloat(latlng.lat.toFixed(7));
    form.longitude = parseFloat(latlng.lng.toFixed(7));
    showAddMarkerModal.value = true;
    showEditMarkerModal.value = false;
  };
  
  const openEditModal = (marker: MarkerData) => {
    form.id = marker.id;
    form.name = marker.name || '';
    form.latitude = marker.latitude;
    form.longitude = marker.longitude;
    form.description = marker.description || '';
    currentEditingMarker.value = marker;
    showEditMarkerModal.value = true;
    showAddMarkerModal.value = false;
  };
  
  const closeModal = () => {
    showAddMarkerModal.value = false;
    showEditMarkerModal.value = false;
    form.reset();
    form.clearErrors();
    currentEditingMarker.value = null;
  };
  
  const submitMarker = () => {
    if (showEditMarkerModal.value && form.id) {
      form.put(route('map.update', form.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
      });
    } else {
      form.post(route('map.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
      });
    }
  };
  
  const deleteMarker = (markerId: number) => {
    if (confirm('Are you sure you want to delete this marker?')) {
      router.delete(route('map.destroy', markerId), {
        preserveScroll: true,
        onSuccess: () => map?.closePopup(), // Close popup after successful deletion
      });
    }
  };
  
  const addMarkersToMap = () => {
    if (!map) return;
    leafletMarkers.value.forEach(m => m.remove());
    leafletMarkers.value = [];
    props.markers.forEach(markerData => {
      const leafletMarker = L.marker([markerData.latitude, markerData.longitude])
        .addTo(map)
        .bindPopup(`
          <div class="p-3.5 bg-white rounded-lg shadow-xl border border-slate-200 min-w-[240px] text-slate-700 font-sans">
            <strong class="block text-md font-semibold text-slate-800 mb-1.5">${markerData.name || 'Unnamed Marker'}</strong>
            <p class="text-sm text-slate-600 mb-3 leading-relaxed">${markerData.description ? markerData.description.replace(/\n/g, '<br>') : '<em>No description.</em>'}</p>
            <div class="mt-3 pt-3 border-t border-slate-200 flex items-center space-x-2">
              <button class="edit-marker-btn flex-grow inline-flex items-center justify-center space-x-1.5 text-xs px-3 py-1.5 rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-1 focus:ring-offset-white transition-colors" data-marker-id="${markerData.id}">
                ${pencilSVG}
                <span>Edit</span>
              </button>
              <button class="delete-marker-btn flex-grow inline-flex items-center justify-center space-x-1.5 text-xs px-3 py-1.5 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 focus:ring-offset-white transition-colors" data-marker-id="${markerData.id}">
                ${trashSVG}
                <span>Delete</span>
              </button>
            </div>
          </div>
        `, { className: 'custom-leaflet-popup' }); // Added custom class
      leafletMarkers.value.push(leafletMarker);
    });
  };
  
  onMounted(() => {
    if (mapContainer.value) {
      map = L.map(mapContainer.value).setView([59.4370, 24.7536], 7);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);
  
      map.on('click', (e: LeafletMouseEvent) => {
        openAddModal(e.latlng);
      });
  
      map.on('popupopen', (e: PopupEvent) => {
        const popupNode = e.popup.getElement();
        if (!popupNode) return;
        
        // Attach event listeners to buttons inside the popup
        const editBtn = popupNode.querySelector('.edit-marker-btn');
        if (editBtn) {
          editBtn.addEventListener('click', (event: Event) => {
            const markerId = (event.currentTarget as HTMLElement).dataset.markerId;
            const markerToEdit = props.markers.find(m => m.id === Number(markerId));
            if (markerToEdit) openEditModal(markerToEdit);
          });
        }
  
        const deleteBtn = popupNode.querySelector('.delete-marker-btn');
        if (deleteBtn) {
          deleteBtn.addEventListener('click', (event: Event) => {
            const markerId = (event.currentTarget as HTMLElement).dataset.markerId;
            if (markerId) {
              deleteMarker(Number(markerId));
              // map?.closePopup(); // Moved to onSuccess of deleteMarker
            }
          });
        }
      });
  
      addMarkersToMap();
    }
  });
  
  watch(() => props.markers, () => {
    if (map) addMarkersToMap();
  }, { deep: true });
  
  onUnmounted(() => {
    if (map) {
      map.remove();
      map = null;
    }
  });
  
  const successMessage = computed(() => (page.props.flash as any)?.success);
  </script>