'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { FiPackage, FiClock, FiCheckCircle, FiXCircle } from 'react-icons/fi';

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  processing: 'bg-blue-100 text-blue-800',
  shipped: 'bg-purple-100 text-purple-800',
  delivered: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
};

const statusIcons = {
  pending: FiClock,
  processing: FiPackage,
  shipped: FiPackage,
  delivered: FiCheckCircle,
  cancelled: FiXCircle,
};

export default function OrdersPage() {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchOrders();
  }, []);

  const fetchOrders = async () => {
    try {
      // In real app, filter by authenticated user
      const res = await fetch('/api/orders');
      const data = await res.json();
      
      if (data.success) {
        setOrders(data.data);
      }
    } catch (error) {
      console.error('Error fetching orders:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <p className="text-gray-500">Loading orders...</p>
      </div>
    );
  }

  if (orders.length === 0) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <FiPackage className="mx-auto text-gray-300 mb-4" size={80} />
        <h2 className="text-2xl font-bold mb-2">No orders yet</h2>
        <p className="text-gray-500 mb-6">Start shopping to create your first order</p>
        <Link
          href="/products"
          className="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700"
        >
          Browse Products
        </Link>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-8">My Orders</h1>

      <div className="space-y-4">
        {orders.map((order) => {
          const StatusIcon = statusIcons[order.status] || FiPackage;
          
          return (
            <div key={order._id} className="bg-white rounded-lg shadow-md p-6">
              <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                  <h3 className="text-lg font-semibold mb-1">
                    Order #{order._id.slice(-8).toUpperCase()}
                  </h3>
                  <p className="text-sm text-gray-500">
                    Placed on {new Date(order.createdAt).toLocaleDateString('en-US', {
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric',
                    })}
                  </p>
                </div>

                <div className="flex items-center gap-3 mt-3 md:mt-0">
                  <span className={`px-3 py-1 rounded-full text-sm font-semibold flex items-center gap-1 ${statusColors[order.status]}`}>
                    <StatusIcon size={16} />
                    {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                  </span>
                  <Link
                    href={`/orders/${order._id}`}
                    className="text-blue-600 hover:underline text-sm font-medium"
                  >
                    View Details
                  </Link>
                </div>
              </div>

              <div className="border-t pt-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                  <div>
                    <p className="text-sm text-gray-600 mb-1">Shipping Address</p>
                    <p className="text-sm font-medium">
                      {order.shippingAddress?.street}, {order.shippingAddress?.city}
                      {order.shippingAddress?.state && `, ${order.shippingAddress.state}`}
                    </p>
                  </div>
                  <div>
                    <p className="text-sm text-gray-600 mb-1">Payment Method</p>
                    <p className="text-sm font-medium capitalize">
                      {order.paymentMethod?.replace('_', ' ')}
                    </p>
                  </div>
                </div>

                <div className="flex items-center justify-between">
                  <p className="text-sm text-gray-600">
                    {order.items?.length || 0} item(s)
                  </p>
                  <p className="text-lg font-bold text-blue-600">
                    ৳{order.totalAmount}
                  </p>
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
