'use client';

import { FiArrowRight } from 'react-icons/fi';
import LiNK from 'next/link';
import { useRouter } from 'next/navigation';

export default function OrderTable({ orders, limit = 5 }) {
  const router = useRouter();
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
  };
  
  const getStatusColor = (status) => {
    switch (status) {
      case 'delivered': return 'bg-green-100 text-green-800';
      case 'shipped': return 'bg-blue-100 text-blue-800';
      case 'processing': return 'bg-yellow-100 text-yellow-800';
      case 'cancelled': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const displayedOrders = Array.isArray(orders) ? orders.slice(0, limit) : [];

  return (
    <div className="overflow-x-auto">
      <table className="min-w-full divide-y divide-gray-200">
        <thead className="bg-gray-50">
          <tr>
            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Order ID
            </th>
            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Customer
            </th>
            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Date
            </th>
            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Amount
            </th>
            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Action
            </th>
          </tr>
        </thead>
        <tbody className="bg-white divide-y divide-gray-200">
          {displayedOrders.length > 0 ? (
            displayedOrders.map((order) => (
              <tr key={order.id} className="hover:bg-gray-50 cursor-pointer" onClick={() => router.push(`/admin/orders/${order.id}`)}>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {order.id.slice(-6).toUpperCase()}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {order.customer}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {formatDate(order.date)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                  {order.amount}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusColor(order.status)}`}>
                    {order.status}
                  </span>
                </td>
                 <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                   <LiNK href={`/admin/orders/${order.id}`} className="text-blue-600 hover:text-blue-900">
                     View
                   </LiNK>
                 </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="6" className="px-6 py-4 text-center text-sm text-gray-500 italic">
                No orders found.
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}
