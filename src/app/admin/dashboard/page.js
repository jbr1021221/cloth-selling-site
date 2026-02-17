'use client';

import { useState, useEffect } from 'react';
import StatsCard from '@/components/admin/StatsCard';
import OrderTable from '@/components/admin/OrderTable';
import { FiShoppingBag, FiUsers, FiPackage, FiDollarSign } from 'react-icons/fi';

export default function AdminDashboardPage() {
  const [stats, setStats] = useState({
    totalRevenue: '৳0',
    totalOrders: 0,
    totalProducts: 0,
    totalCustomers: 0,
  });
  const [recentOrders, setRecentOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchDashboardData() {
      try {
        // In a real application, you would fetch this from an API endpoint
        // For now, we'll use some mock data or fetch from existing endpoints if available
        
        // Simulating data fetching
        const response = await fetch('/api/orders');
        const orderData = await response.json();
        
        const productsRes = await fetch('/api/products');
        const productData = await productsRes.json();

        if (orderData.success) {
          const totalRev = orderData.data.reduce((acc, order) => acc + order.totalAmount, 0);
          const formattedOrders = orderData.data.map(order => ({
            id: order._id,
            customer: order.customerInfo?.name || 'Unknown',
            date: order.createdAt,
            amount: `৳${order.totalAmount}`,
            status: order.status
          }));

          setStats({
            totalRevenue: `৳${totalRev}`,
            totalOrders: orderData.data.length,
            totalProducts: productData.success ? productData.data.length : 0,
            totalCustomers: [...new Set(orderData.data.map(o => o.user))].length,
          });
          setRecentOrders(formattedOrders);
        }
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
      } finally {
        setLoading(false);
      }
    }

    fetchDashboardData();
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
        <div className="text-sm text-gray-500">
          Last updated: {new Date().toLocaleString()}
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatsCard
          title="Total Revenue"
          value={stats.totalRevenue}
          icon={FiDollarSign}
          color="green"
          description="Total sales volume"
        />
        <StatsCard
          title="Total Orders"
          value={stats.totalOrders}
          icon={FiShoppingBag}
          color="blue"
          description="Orders placed so far"
        />
        <StatsCard
          title="Total Products"
          value={stats.totalProducts}
          icon={FiPackage}
          color="purple"
          description="Active products in store"
        />
        <StatsCard
          title="Customers"
          value={stats.totalCustomers}
          icon={FiUsers}
          color="orange"
          description="Unique customers"
        />
      </div>

      <div className="bg-white rounded-lg shadow-md p-6">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-xl font-bold text-gray-800">Recent Orders</h2>
          <button 
            onClick={() => window.location.href = '/admin/orders'}
            className="text-blue-600 hover:text-blue-800 font-medium text-sm"
          >
            View All
          </button>
        </div>
        <OrderTable orders={recentOrders} limit={8} />
      </div>
    </div>
  );
}
