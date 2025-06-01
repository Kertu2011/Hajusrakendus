import { Reactive, reactive } from 'vue';
import type { CartItem } from '@/types';

export const cart: Reactive<CartItem[]> = reactive({
    items: [],

    addItem(product, quantity = 1) {
        if (quantity <= 0) return;
        const existingItem = this.items.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.items.push({ ...product, quantity });
        }
        this.saveCartToLocalStorage();
    },

    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.saveCartToLocalStorage();
    },

    updateQuantity(productId, quantity) {
        if (quantity <= 0) {
            this.removeItem(productId);
            return;
        }
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = quantity;
        }
        this.saveCartToLocalStorage();
    },

    clearCart() {
        this.items = [];
        this.saveCartToLocalStorage();
    },

    get totalItems() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    },

    get totalPrice() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);
    },

    saveCartToLocalStorage() {
        localStorage.setItem('shopping_cart', JSON.stringify(this.items));
    },

    loadCartFromLocalStorage() {
        const savedCart = localStorage.getItem('shopping_cart');
        if (savedCart) {
            this.items = JSON.parse(savedCart);
        }
    }
});

// Load cart from local storage when the store is initialized
cart.loadCartFromLocalStorage();
