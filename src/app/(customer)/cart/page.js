'use client';

import { useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { FiTrash2, FiMinus, FiPlus, FiShoppingBag } from 'react-icons/fi';
import useCartStore from '@/store/cartStore';
import { useRouter } from 'next/navigation';

export default function CartPage() {
  const router = useRouter();
  const { items, removeItem, updateQuantity, getTotal, clearCart } = useCartStore();

  const subtotal = getTotal();
  const shippingCharge = subtotal > 0 ? (subtotal >= 1000 ? 0 : 60) : 0;
  const total = subtotal + shippingCharge;

  const handleQuantityChange = (item, newQuantity) => {
    if (newQuantity < 1) return;
    updateQuantity(item.id, item.size, item.color, newQuantity);
  };

  const handleRemove = (item) => {
    removeItem(item.id, item.size, item.color);
  };

  if (items.length === 0) {
    return (
      <div className="container mx-auto px-4 py-16">
        <div className="text-center">
          <FiShoppingBag className="mx-auto text-gray-300 mb-4" size={80} />
          <h2 className="text-2xl font-bold mb-2">Your cart is empty</h2>
          <p className="text-gray-500 mb-6">
            Add some products to get started
          </p>
          <Link
            href="/products"
            className="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700"
          >
            Continue Shopping
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-8">Shopping Cart</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Cart Items */}
        <div className="lg:col-span-2">
          <div className="bg-white rounded-lg shadow-md">
            {items.map((item) => (
              <div
                key={`${item.id}-${item.size}-${item.color}`}
                className="flex gap-4 p-4 border-b last:border-b-0"
              >
                {/* Image */}
                <div className="relative w-24 h-24 flex-shrink-0 rounded overflow-hidden">
                  <Image
                    src={item.image || '/placeholder.jpg'}
                    alt={item.name}
                    fill
                    className="object-cover"
                  />
                </div>

                {/* Details */}
                <div className="flex-1">
                  <h3 className="font-semibold mb-1">{item.name}</h3>
                  <p className="text-sm text-gray-500 mb-2">
                    Size: {item.size} | Color: {item.color}
                  </p>
                  <p className="text-blue-600 font-semibold">৳{item.price}</p>
                </div>

                {/* Quantity Controls */}
                <div className="flex flex-col items-end justify-between">
                  <button
                    onClick={() => handleRemove(item)}
                    className="text-red-500 hover:text-red-700"
                  >
                    <FiTrash2 size={20} />
                  </button>

                  <div className="flex items-center gap-2 border rounded">
                    <button
                      onClick={() => handleQuantityChange(item, item.quantity - 1)}
                      className="p-2 hover:bg-gray-100"
                    >
                      <FiMinus size={16} />
                    </button>
                    <span className="w-12 text-center font-semibold">
                      {item.quantity}
                    </span>
                    <button
                      onClick={() => handleQuantityChange(item, item.quantity + 1)}
                      className="p-2 hover:bg-gray-100"
                    >
                      <FiPlus size={16} />
                    </button>
                  </div>

                  <p className="font-bold">
                    ৳{item.price * item.quantity}
                  </p>
                </div>
              </div>
            ))}
          </div>

          <button
            onClick={clearCart}
            className="mt-4 text-red-500 hover:text-red-700 text-sm"
          >
            Clear Cart
          </button>
        </div>

        {/* Order Summary */}
        <div className="lg:col-span-1">
          <div className="bg-white rounded-lg shadow-md p-6 sticky top-20">
            <h2 className="text-xl font-bold mb-4">Order Summary</h2>

            <div className="space-y-3 mb-4">
              <div className="flex justify-between">
                <span className="text-gray-600">Subtotal</span>
                <span className="font-semibold">৳{subtotal}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-600">Shipping</span>
                <span className="font-semibold">
                  {shippingCharge === 0 ? 'FREE' : `৳${shippingCharge}`}
                </span>
              </div>
              {subtotal < 1000 && subtotal > 0 && (
                <p className="text-sm text-green-600">
                  Add ৳{1000 - subtotal} more for free shipping!
                </p>
              )}
              <div className="border-t pt-3 flex justify-between text-lg font-bold">
                <span>Total</span>
                <span className="text-blue-600">৳{total}</span>
              </div>
            </div>

            <Link
              href="/checkout"
              className="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 font-semibold"
            >
              Proceed to Checkout
            </Link>

            <Link
              href="/products"
              className="block w-full text-center text-blue-600 py-2 mt-2 hover:underline"
            >
              Continue Shopping
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}