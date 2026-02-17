'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import Image from 'next/image';
import Link from 'next/link';
import { FiPackage, FiMapPin, FiCreditCard, FiUser, FiPhone, FiMail } from 'react-icons/fi';

const statusSteps = ['pending', 'processing', 'shipped', 'delivered'];

const statusColors = {
  pending: 'bg-yellow-100 text-yellow-800 border-yellow-300',
  processing: 'bg-blue-100 text-blue-800 border-blue-300',
  shipped: 'bg-purple-100 text-purple-800 border-purple-300',
  delivered: 'bg-green-100 text-green-800 border-green-300',
  cancelled: 'bg-red-100 text-red-800 border-red-300',
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
      
      if (data.success) {
        setOrder(data.data);
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

  const currentStepIndex = statusSteps.indexOf(order.status);

  return (
    <div className="container mx-auto px-4 py-8">
      {/* Header */}
      <div className="mb-8">
        <Link href="/orders" className="text-blue-600 hover:underline mb-4 inline-block">
          ← Back to Orders
        </Link>
        <div className="flex flex-col md:flex-row md:items-center md:justify-between">
          <div>
            <h1 className="text-3xl font-bold mb-2">
              Order #{order._id.slice(-8).toUpperCase()}
            </h1>
            <p className="text-gray-600">
              Placed on {new Date(order.createdAt).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
              })}
            </p>
          </div>
          <span className={`px-4 py-2 rounded-lg font-semibold border-2 mt-4 md:mt-0 ${statusColors[order.status]}`}>
            {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
          </span>
        </div>
      </div>

      {/* Order Progress */}
      {order.status !== 'cancelled' && (
        <div className="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 className="text-xl font-semibold mb-6">Order Progress</h2>
          <div className="flex justify-between items-center">
            {statusSteps.map((step, index) => (
              <div key={step} className="flex-1 flex flex-col items-center">
                <div className={`w-10 h-10 rounded-full flex items-center justify-center font-semibold ${
                  index <= currentStepIndex ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'
                }`}>
                  {index + 1}
                </div>
                <p className={`text-sm mt-2 capitalize ${
                  index <= currentStepIndex ? 'text-blue-600 font-semibold' : 'text-gray-500'
                }`}>
                  {step}
                </p>
                {index < statusSteps.length - 1 && (
                  <div className={`h-1 w-full mt-5 -ml-full ${
                    index < currentStepIndex ? 'bg-blue-600' : 'bg-gray-200'
                  }`} />
                )}
              </div>
            ))}
          </div>
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Order Items */}
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-semibold mb-4 flex items-center gap-2">
              <FiPackage />
              Order Items
            </h2>
            <div className="space-y-4">
              {order.items?.map((item, index) => (
                <div key={index} className="flex gap-4 pb-4 border-b last:border-b-0">
                  <div className="relative w-20 h-20 flex-shrink-0 rounded overflow-hidden bg-gray-100">
                    <Image
                      src={item.product?.images?.[0] || '/placeholder.jpg'}
                      alt={item.product?.name || 'Product'}
                      fill
                      className="object-cover"
                    />
                  </div>
                  <div className="flex-1">
                    <h3 className="font-semibold mb-1">
                      {item.product?.name || 'Product'}
                    </h3>
                    <p className="text-sm text-gray-500">
                      Size: {item.size} | Color: {item.color}
                    </p>
                    <p className="text-sm text-gray-500">Quantity: {item.quantity}</p>
                  </div>
                  <div className="text-right">
                    <p className="font-bold text-blue-600">৳{item.price * item.quantity}</p>
                    <p className="text-sm text-gray-500">৳{item.price} each</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Customer Information */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-semibold mb-4 flex items-center gap-2">
              <FiUser />
              Customer Information
            </h2>
            <div className="space-y-3">
              <div className="flex items-center gap-2">
                <FiUser className="text-gray-400" />
                <span>{order.customerInfo?.name || 'N/A'}</span>
              </div>
              <div className="flex items-center gap-2">
                <FiMail className="text-gray-400" />
                <span>{order.customerInfo?.email || 'N/A'}</span>
              </div>
              <div className="flex items-center gap-2">
                <FiPhone className="text-gray-400" />
                <span>{order.customerInfo?.phone || 'N/A'}</span>
              </div>
            </div>
          </div>
        </div>

        {/* Order Summary Sidebar */}
        <div className="lg:col-span-1 space-y-6">
          {/* Shipping Address */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-semibold mb-4 flex items-center gap-2">
              <FiMapPin />
              Shipping Address
            </h2>
            <p className="text-gray-700 leading-relaxed">
              {order.shippingAddress?.street}<br />
              {order.shippingAddress?.city}, {order.shippingAddress?.state}<br />
              {order.shippingAddress?.zip && `${order.shippingAddress.zip}`}
            </p>
          </div>

          {/* Payment Info */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-semibold mb-4 flex items-center gap-2">
              <FiCreditCard />
              Payment Method
            </h2>
            <p className="text-gray-700 capitalize">
              {order.paymentMethod?.replace('_', ' ')}
            </p>
            <p className={`text-sm mt-2 font-semibold ${
              order.paymentStatus === 'paid' ? 'text-green-600' : 'text-yellow-600'
            }`}>
              {order.paymentStatus === 'paid' ? 'Paid' : 'Pending Payment'}
            </p>
          </div>

          {/* Order Summary */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-semibold mb-4">Order Summary</h2>
            <div className="space-y-2">
              <div className="flex justify-between">
                <span className="text-gray-600">Subtotal</span>
                <span className="font-semibold">৳{order.totalAmount}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-600">Shipping</span>
                <span className="font-semibold">Included</span>
              </div>
              <div className="border-t pt-2 flex justify-between text-lg font-bold">
                <span>Total</span>
                <span className="text-blue-600">৳{order.totalAmount}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
