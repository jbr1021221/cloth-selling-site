'use client';

import { useState, useEffect } from 'react';
import OrderTable from '@/components/admin/OrderTable';
import { FiSearch, FiFilter } from 'react-icons/fi';
import toast from 'react-hot-toast';

export default function AdminOrdersPage() {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [filterStatus, setFilterStatus] = useState('all');

  useEffect(() => {
    fetchOrders();
  }, [filterStatus]);

  const fetchOrders = async () => {
    try {
      setLoading(true);
      let url = '/api/orders';
      if (filterStatus !== 'all') {
        url += `?status=${filterStatus}`;
      }
      
      const res = await fetch(url);
      const data = await res.json();
      
      if (data.success) {
        const formattedOrders = data.data.map(order => ({
          id: order._id,
          customer: order.customerInfo?.name || 'Unknown',
          date: order.createdAt,
          amount: `৳${order.totalAmount}`,
          status: order.status
        }));
        setOrders(formattedOrders);
      }
    } catch (error) {
      toast.error('Failed to fetch orders');
    } finally {
      setLoading(false);
    }
  };

  const filteredOrders = orders.filter(o => 
    o.id.toLowerCase().includes(searchTerm.toLowerCase()) ||
    o.customer.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 className="text-3xl font-bold text-gray-800">Order Management</h1>
        <div className="flex items-center gap-2">
          <FiFilter className="text-gray-400" />
          <select 
            className="border rounded-lg px-3 py-2 bg-white outline-none focus:ring-2 focus:ring-blue-500"
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value)}
          >
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>

      <div className="relative">
        <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
          <FiSearch size={18} />
        </span>
        <input
          type="text"
          placeholder="Search by Order ID or Customer Name..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
        />
      </div>

      <div className="bg-white rounded-lg shadow-md">
        {loading ? (
          <div className="p-8 text-center text-gray-500 italic">Updating list...</div>
        ) : (
          <OrderTable orders={filteredOrders} limit={filteredOrders.length} />
        )}
      </div>
    </div>
  );
}
