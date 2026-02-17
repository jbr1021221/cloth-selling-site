'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import useCartStore from '@/store/cartStore';
import toast from 'react-hot-toast';

export default function CheckoutPage() {
  const router = useRouter();
  const { items, getTotal, clearCart } = useCartStore();
  const [loading, setLoading] = useState(false);
  
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    address: {
      street: '',
      city: '',
      state: '',
      zip: '',
    },
    paymentMethod: 'cash_on_delivery',
  });

  const subtotal = getTotal();
  const shippingCharge = subtotal >= 1000 ? 0 : 60;
  const total = subtotal + shippingCharge;

  const handleChange = (e) => {
    const { name, value } = e.target;
    
    if (name.startsWith('address.')) {
      const addressField = name.split('.')[1];
      setFormData({
        ...formData,
        address: { ...formData.address, [addressField]: value },
      });
    } else {
      setFormData({ ...formData, [name]: value });
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    try {
      const orderData = {
        user: formData.email, // In real app, use authenticated user ID
        items: items.map(item => ({
          product: item.id,
          quantity: item.quantity,
          price: item.price,
          size: item.size,
          color: item.color,
        })),
        totalAmount: total,
        shippingAddress: formData.address,
        paymentMethod: formData.paymentMethod,
        customerInfo: {
          name: formData.name,
          email: formData.email,
          phone: formData.phone,
        },
      };

      const res = await fetch('/api/orders', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData),
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.error || 'Order failed');
      }

      toast.success('Order placed successfully!');
      clearCart();
      router.push(`/orders/${data.data._id}`);
    } catch (error) {
      toast.error(error.message);
    } finally {
      setLoading(false);
    }
  };

  if (items.length === 0) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <h2 className="text-2xl font-bold mb-4">Your cart is empty</h2>
        <p className="text-gray-500 mb-6">Add some products before checkout</p>
        <button
          onClick={() => router.push('/products')}
          className="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700"
        >
          Continue Shopping
        </button>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-8">Checkout</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Checkout Form */}
        <div className="lg:col-span-2">
          <form onSubmit={handleSubmit} className="bg-white rounded-lg shadow-md p-6">
            {/* Customer Information */}
            <div className="mb-6">
              <h2 className="text-xl font-semibold mb-4">Customer Information</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-1">Full Name *</label>
                  <input
                    type="text"
                    name="name"
                    required
                    value={formData.name}
                    onChange={handleChange}
                    className="w-full border rounded-lg px-3 py-2"
                    placeholder="John Doe"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Email *</label>
                  <input
                    type="email"
                    name="email"
                    required
                    value={formData.email}
                    onChange={handleChange}
                    className="w-full border rounded-lg px-3 py-2"
                    placeholder="john@example.com"
                  />
                </div>
                <div className="md:col-span-2">
                  <label className="block text-sm font-medium mb-1">Phone *</label>
                  <input
                    type="tel"
                    name="phone"
                    required
                    value={formData.phone}
                    onChange={handleChange}
                    className="w-full border rounded-lg px-3 py-2"
                    placeholder="01712345678"
                  />
                </div>
              </div>
            </div>

            {/* Shipping Address */}
            <div className="mb-6">
              <h2 className="text-xl font-semibold mb-4">Shipping Address</h2>
              <div className="grid grid-cols-1 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-1">Street Address *</label>
                  <input
                    type="text"
                    name="address.street"
                    required
                    value={formData.address.street}
                    onChange={handleChange}
                    className="w-full border rounded-lg px-3 py-2"
                    placeholder="123 Main St"
                  />
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium mb-1">City *</label>
                    <input
                      type="text"
                      name="address.city"
                      required
                      value={formData.address.city}
                      onChange={handleChange}
                      className="w-full border rounded-lg px-3 py-2"
                      placeholder="Dhaka"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium mb-1">State/Division *</label>
                    <input
                      type="text"
                      name="address.state"
                      required
                      value={formData.address.state}
                      onChange={handleChange}
                      className="w-full border rounded-lg px-3 py-2"
                      placeholder="Dhaka"
                    />
                  </div>
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">ZIP Code</label>
                  <input
                    type="text"
                    name="address.zip"
                    value={formData.address.zip}
                    onChange={handleChange}
                    className="w-full border rounded-lg px-3 py-2"
                    placeholder="1200"
                  />
                </div>
              </div>
            </div>

            {/* Payment Method */}
            <div className="mb-6">
              <h2 className="text-xl font-semibold mb-4">Payment Method</h2>
              <div className="space-y-2">
                <label className="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                  <input
                    type="radio"
                    name="paymentMethod"
                    value="cash_on_delivery"
                    checked={formData.paymentMethod === 'cash_on_delivery'}
                    onChange={handleChange}
                    className="mr-3"
                  />
                  <span className="font-medium">Cash on Delivery</span>
                </label>
                <label className="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                  <input
                    type="radio"
                    name="paymentMethod"
                    value="bkash"
                    checked={formData.paymentMethod === 'bkash'}
                    onChange={handleChange}
                    className="mr-3"
                  />
                  <span className="font-medium">bKash</span>
                </label>
                <label className="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                  <input
                    type="radio"
                    name="paymentMethod"
                    value="ssl_commerz"
                    checked={formData.paymentMethod === 'ssl_commerz'}
                    onChange={handleChange}
                    className="mr-3"
                  />
                  <span className="font-medium">SSLCommerz</span>
                </label>
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:bg-gray-400"
            >
              {loading ? 'Placing Order...' : 'Place Order'}
            </button>
          </form>
        </div>

        {/* Order Summary */}
        <div className="lg:col-span-1">
          <div className="bg-white rounded-lg shadow-md p-6 sticky top-20">
            <h2 className="text-xl font-semibold mb-4">Order Summary</h2>

            <div className="space-y-3 mb-4">
              {items.map((item) => (
                <div key={`${item.id}-${item.size}-${item.color}`} className="flex justify-between text-sm">
                  <span className="text-gray-600">
                    {item.name} x {item.quantity}
                  </span>
                  <span className="font-semibold">৳{item.price * item.quantity}</span>
                </div>
              ))}
            </div>

            <div className="border-t pt-3 space-y-2">
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
              <div className="border-t pt-2 flex justify-between text-lg font-bold">
                <span>Total</span>
                <span className="text-blue-600">৳{total}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
