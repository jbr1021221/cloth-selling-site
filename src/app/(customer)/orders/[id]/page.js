'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import Image from 'next/image';
import Link from 'next/link';
import { FiPackage, FiMapPin, FiCreditCard, FiUser, FiPhone, FiMail } from 'react-icons/fi';

const statusSteps = ['pending', 'processing', 'shipped', 'delivered'];

const statusColors = {
  pending: 'bg-yellow-900/30 text-yellow-500 border-yellow-700 border',
  processing: 'bg-blue-900/30 text-blue-400 border-blue-700 border',
  shipped: 'bg-purple-900/30 text-purple-400 border-purple-700 border',
  delivered: 'bg-green-900/30 text-green-400 border-green-700 border',
  cancelled: 'bg-red-900/30 text-red-400 border-red-700 border',
};

export default function OrderDetailPage({ params }) {
  const router = useRouter();
  const [order, setOrder] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchOrder();
  }, [params.id]);

  const fetchOrder = async () => {
    try {
      const res = await fetch(`/api/orders/${params.id}`);
      const data = await res.json();

      if (res.ok) {
        setOrder(data);
      } else {
        router.push('/orders');
      }
    } catch (error) {
      console.error('Error fetching order:', error);
      router.push('/orders');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <p className="text-gray-500">Loading order details...</p>
      </div>
    );
  }

  if (!order) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <h2 className="text-2xl font-bold mb-4">Order Not Found</h2>
        <Link href="/orders" className="text-blue-600 hover:underline">
          Back to Orders
        </Link>
      </div>
    );
  }

  // Helper to safely access delivery address
  const address = order.delivery_address || {};

  return (
    <div className="container mx-auto px-4 py-8 text-gray-100">
      {/* Header */}
      <div className="mb-8">
        <Link href="/orders" className="text-blue-400 hover:text-blue-300 hover:underline mb-4 inline-block transition-colors">
          ← Back to Orders
        </Link>
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 className="text-3xl font-bold mb-2 text-white">
              Order #{order.order_number || (order.id ? `ORD-${order.id}` : 'N/A')}
            </h1>
            <p className="text-gray-400">
              Placed on {new Date(order.created_at || order.createdAt).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
              })}
            </p>
          </div>
          <span className={`px-4 py-2 rounded-lg font-semibold capitalize ${statusColors[order.status.toLowerCase()] || 'bg-gray-800 text-gray-400 border border-gray-700'}`}>
            {order.status}
          </span>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Order Items & Customer Info */}
        <div className="lg:col-span-2 space-y-6">
          {/* Order Items */}
          <div className="bg-gray-800 rounded-lg shadow-xl p-6 border border-gray-700">
            <h2 className="text-xl font-semibold mb-6 flex items-center gap-2 text-white">
              <FiPackage className="text-blue-400" />
              Order Items
            </h2>
            <div className="space-y-6">
              {order.items?.map((item, index) => (
                <div key={index} className="flex flex-col sm:flex-row gap-4 pb-6 border-b border-gray-700 last:border-b-0 last:pb-0">
                  <div className="relative w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-700 border border-gray-600">
                    <Image
                      src={item.image || '/placeholder.jpg'}
                      alt={item.name}
                      fill
                      className="object-cover"
                    />
                  </div>
                  <div className="flex-1">
                    <h3 className="font-semibold mb-1 text-gray-100 text-lg">
                      {item.name}
                    </h3>
                    <div className="flex flex-wrap gap-3 text-sm text-gray-400 mb-2">
                      <span className="bg-gray-700 px-2 py-0.5 rounded border border-gray-600">Size: {item.size}</span>
                      <span className="bg-gray-700 px-2 py-0.5 rounded border border-gray-600">Color: {item.color}</span>
                    </div>
                    <p className="text-gray-400">Qty: {item.quantity}</p>
                  </div>
                  <div className="sm:text-right flex flex-row sm:flex-col justify-between sm:justify-start items-center sm:items-end">
                    <p className="font-bold text-blue-400 text-lg">৳{item.price * item.quantity}</p>
                    <p className="text-sm text-gray-500">৳{item.price} each</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Customer Information */}
          <div className="bg-gray-800 rounded-lg shadow-xl p-6 border border-gray-700">
            <h2 className="text-xl font-semibold mb-6 flex items-center gap-2 text-white">
              <FiUser className="text-purple-400" />
              Customer Information
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
              <div className="flex items-center gap-3 p-3 rounded-lg bg-gray-700/30">
                <div className="p-2 rounded-full bg-gray-700 text-gray-300">
                  <FiUser />
                </div>
                <div>
                  <p className="text-xs text-gray-500 uppercase tracking-wider font-semibold">Name</p>
                  <p className="text-gray-200">{address.name || order.user?.name || 'N/A'}</p>
                </div>
              </div>
              <div className="flex items-center gap-3 p-3 rounded-lg bg-gray-700/30">
                <div className="p-2 rounded-full bg-gray-700 text-gray-300">
                  <FiMail />
                </div>
                <div>
                  <p className="text-xs text-gray-500 uppercase tracking-wider font-semibold">Email</p>
                  <p className="text-gray-200">{address.email || order.user?.email || 'N/A'}</p>
                </div>
              </div>
              <div className="flex items-center gap-3 p-3 rounded-lg bg-gray-700/30 md:col-span-2">
                <div className="p-2 rounded-full bg-gray-700 text-gray-300">
                  <FiPhone />
                </div>
                <div>
                  <p className="text-xs text-gray-500 uppercase tracking-wider font-semibold">Phone</p>
                  <p className="text-gray-200">{address.phone || 'N/A'}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Sidebar */}
        <div className="lg:col-span-1 space-y-6">
          {/* Shipping Address */}
          <div className="bg-gray-800 rounded-lg shadow-xl p-6 border border-gray-700">
            <h2 className="text-xl font-semibold mb-4 flex items-center gap-2 text-white">
              <FiMapPin className="text-red-400" />
              Shipping Address
            </h2>
            <div className="p-4 bg-gray-700/30 rounded-lg border border-gray-700">
              <p className="text-gray-300 leading-relaxed">
                <span className="block text-white font-medium mb-1">{address.street}</span>
                <span className="block">{address.city}, {address.state}</span>
                <span className="block text-gray-500 text-sm mt-1">ZIP: {address.zip}</span>
              </p>
            </div>
          </div>

          {/* Payment Info */}
          <div className="bg-gray-800 rounded-lg shadow-xl p-6 border border-gray-700">
            <h2 className="text-xl font-semibold mb-4 flex items-center gap-2 text-white">
              <FiCreditCard className="text-green-400" />
              Payment Details
            </h2>
            <div className="flex justify-between items-center p-3 bg-gray-700/30 rounded-lg mb-3 border border-gray-700">
              <span className="text-gray-400">Method</span>
              <span className="text-white font-medium capitalize">
                {order.payment_method?.replace(/_/g, ' ') || order.paymentMethod}
              </span>
            </div>
            <div className="flex justify-between items-center p-3 bg-gray-700/30 rounded-lg border border-gray-700">
              <span className="text-gray-400">Status</span>
              <span className={`font-bold ${(order.payment_status || order.paymentStatus) === 'Paid' ? 'text-green-400' : 'text-yellow-400'}`}>
                {order.payment_status || order.paymentStatus || 'Pending'}
              </span>
            </div>
          </div>

          {/* Order Summary */}
          <div className="bg-gray-800 rounded-lg shadow-xl p-6 border border-gray-700">
            <h2 className="text-xl font-semibold mb-4 text-white">Order Summary</h2>
            <div className="space-y-3">
              <div className="flex justify-between items-center text-gray-400">
                <span>Subtotal</span>
                <span className="text-gray-200 font-medium">৳{order.total_amount || order.totalAmount}</span>
              </div>
              <div className="flex justify-between items-center text-gray-400">
                <span>Shipping</span>
                <span className="text-gray-200 font-medium">৳{order.shipping_charge || order.shippingCharge || 0}</span>
              </div>
              <div className="border-t border-gray-700 pt-4 mt-4 flex justify-between items-center text-lg">
                <span className="font-bold text-white">Total</span>
                <span className="font-bold text-blue-400 text-xl">৳{order.final_amount || order.finalAmount}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
