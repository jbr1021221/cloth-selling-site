'use client';

import { useEffect, useState, use } from 'react';
import { useRouter } from 'next/navigation';
import Image from 'next/image';
import { FiPackage, FiTruck, FiCheckCircle, FiXCircle, FiArrowLeft, FiUser, FiPhone, FiMail, FiMapPin } from 'react-icons/fi';
import toast from 'react-hot-toast';

export default function AdminOrderDetailPage({ params }) {
  const router = useRouter();
  const resolvedParams = use(params);
  const id = resolvedParams.id;
  
  const [order, setOrder] = useState(null);
  const [loading, setLoading] = useState(true);
  const [updating, setUpdating] = useState(false);

  useEffect(() => {
    fetchOrder();
  }, [id]);

  const fetchOrder = async () => {
    try {
      const res = await fetch(`/api/orders/${id}`);
      const data = await res.json();
      if (data.success) {
        setOrder(data.data);
      } else {
        toast.error(data.error);
      }
    } catch (error) {
      toast.error('Failed to fetch order');
    } finally {
      setLoading(false);
    }
  };

  const handleStatusUpdate = async (newStatus) => {
    setUpdating(true);
    try {
      const res = await fetch(`/api/orders/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: newStatus }),
      });
      const data = await res.json();
      if (data.success) {
        setOrder(data.data);
        toast.success(`Order status updated to ${newStatus}`);
      } else {
        toast.error(data.error);
      }
    } catch (error) {
      toast.error('Update failed');
    } finally {
      setUpdating(false);
    }
  };

  if (loading) return <div className="p-8 text-center italic text-gray-500">Loading order details...</div>;
  if (!order) return <div className="p-8 text-center text-red-500">Order not found</div>;

  const statusColors = {
    'Pending': 'bg-yellow-100 text-yellow-800',
    'Processing': 'bg-blue-100 text-blue-800',
    'Shipped': 'bg-purple-100 text-purple-800',
    'Delivered': 'bg-green-100 text-green-800',
    'Cancelled': 'bg-red-100 text-red-800',
  };

  return (
    <div className="space-y-6 max-w-6xl mx-auto">
      <button 
        onClick={() => router.back()}
        className="flex items-center gap-2 text-gray-600 hover:text-blue-600 transition"
      >
        <FiArrowLeft /> Back to Orders
      </button>

      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-xl shadow-sm border">
        <div>
          <h1 className="text-2xl font-bold text-gray-800">Order #{order.orderNumber}</h1>
          <p className="text-sm text-gray-500">Placed on {new Date(order.createdAt).toLocaleString()}</p>
        </div>
        <div className="flex flex-wrap gap-3">
          <select 
            disabled={updating}
            value={order.status}
            onChange={(e) => handleStatusUpdate(e.target.value)}
            className={`px-4 py-2 rounded-lg font-bold border-2 outline-none focus:ring-2 focus:ring-blue-500 ${statusColors[order.status]}`}
          >
            <option value="Pending">Pending</option>
            <option value="Processing">Processing</option>
            <option value="Shipped">Shipped</option>
            <option value="Delivered">Delivered</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left Column: Items & Summary */}
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div className="px-6 py-4 border-b bg-gray-50 font-bold text-gray-700">Order Items</div>
            <div className="divide-y">
              {order.items.map((item, idx) => (
                <div key={idx} className="p-6 flex gap-4 items-center">
                  <div className="relative h-20 w-20 flex-shrink-0">
                    <Image 
                      src={item.image || '/placeholder.jpg'} 
                      alt={item.name} 
                      fill 
                      className="object-cover rounded-lg"
                    />
                  </div>
                  <div className="flex-1">
                    <h4 className="font-bold text-gray-800">{item.name}</h4>
                    <p className="text-sm text-gray-500">Size: {item.size} | Color: {item.color}</p>
                    <p className="text-sm text-gray-500">Qty: {item.quantity}</p>
                  </div>
                  <div className="text-right">
                    <p className="font-bold text-gray-900">৳{item.price * item.quantity}</p>
                    <p className="text-xs text-gray-400">৳{item.price} each</p>
                  </div>
                </div>
              ))}
            </div>
            <div className="p-6 bg-gray-50 border-t space-y-2">
              <div className="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <span>৳{order.totalAmount}</span>
              </div>
              <div className="flex justify-between text-sm text-gray-600">
                <span>Shipping Fee</span>
                <span>৳{order.shippingCharge}</span>
              </div>
              <div className="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t">
                <span>Final Total</span>
                <span className="text-blue-600">৳{order.finalAmount}</span>
              </div>
            </div>
          </div>
        </div>

        {/* Right Column: Customer & Shipping */}
        <div className="space-y-6">
           <div className="bg-white rounded-xl shadow-sm border overflow-hidden">
             <div className="px-6 py-4 border-b bg-gray-50 font-bold text-gray-700">Customer Info</div>
             <div className="p-6 space-y-4">
                <div className="flex items-start gap-3">
                  <FiUser className="mt-1 text-gray-400" />
                  <div>
                    <p className="text-sm font-bold text-gray-800">{order.deliveryAddress?.name}</p>
                    <p className="text-xs text-gray-500">User ID: {order.user?._id || 'Guest'}</p>
                  </div>
                </div>
                <div className="flex items-center gap-3">
                  <FiPhone className="text-gray-400" />
                  <p className="text-sm text-gray-700">{order.deliveryAddress?.phone}</p>
                </div>
                <div className="flex items-center gap-3">
                   <FiMail className="text-gray-400" />
                   <p className="text-sm text-gray-700">{order.user?.email || 'N/A'}</p>
                </div>
             </div>
           </div>

           <div className="bg-white rounded-xl shadow-sm border overflow-hidden">
             <div className="px-6 py-4 border-b bg-gray-50 font-bold text-gray-700">Shipping Address</div>
             <div className="p-6 space-y-2">
                <div className="flex items-start gap-3 text-sm text-gray-700">
                  <FiMapPin className="mt-1 text-gray-400" />
                  <p>
                    {order.deliveryAddress?.address}<br />
                    {order.deliveryAddress?.city}, {order.deliveryAddress?.postalCode}
                  </p>
                </div>
             </div>
           </div>

           <div className="bg-white rounded-xl shadow-sm border overflow-hidden">
             <div className="px-6 py-4 border-b bg-gray-50 font-bold text-gray-700">Payment Information</div>
             <div className="p-6 space-y-4">
                <div className="flex justify-between items-center text-sm">
                  <span className="text-gray-500">Method:</span>
                  <span className="font-bold text-gray-800 uppercase">{order.paymentMethod}</span>
                </div>
                <div className="flex justify-between items-center text-sm">
                  <span className="text-gray-500">Status:</span>
                  <span className={`px-2 py-1 rounded text-xs font-bold ${
                    order.paymentStatus === 'Paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                  }`}>
                    {order.paymentStatus}
                  </span>
                </div>
             </div>
           </div>
        </div>
      </div>
    </div>
  );
}
