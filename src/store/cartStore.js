import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';

const useCartStore = create(
  persist(
    (set, get) => ({
      items: [],

      addItem: (product, quantity = 1, size, color) => {
        const items = get().items;
        const productId = product.id || product._id;
        const existingItemIndex = items.findIndex(
          item => item.id === productId && item.size === size && item.color === color
        );

        if (existingItemIndex > -1) {
          const updatedItems = [...items];
          updatedItems[existingItemIndex].quantity += quantity;
          set({ items: updatedItems });
        } else {
          set({
            items: [
              ...items,
              {
                id: productId,
                name: product.name,
                price: product.discountPrice || product.discount_price || product.price,
                image: product.images?.[0] || product.image,
                quantity,
                size,
                color,
              },
            ],
          });
        }
      },

      removeItem: (id, size, color) => {
        set({
          items: get().items.filter(
            item => !(item.id === id && item.size === size && item.color === color)
          ),
        });
      },

      updateQuantity: (id, size, color, quantity) => {
        const items = get().items;
        const itemIndex = items.findIndex(
          item => item.id === id && item.size === size && item.color === color
        );

        if (itemIndex > -1) {
          const updatedItems = [...items];
          updatedItems[itemIndex].quantity = quantity;
          set({ items: updatedItems });
        }
      },

      clearCart: () => set({ items: [] }),

      getTotal: () => {
        return get().items.reduce((total, item) => total + item.price * item.quantity, 0);
      },

      getItemCount: () => {
        return get().items.reduce((count, item) => count + item.quantity, 0);
      },
    }),
    {
      name: 'cart-storage',
      storage: createJSONStorage(() => localStorage),
    }
  )
);

export default useCartStore;