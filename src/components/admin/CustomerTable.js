'use client';

import { FiMail, FiPhone, FiMapPin } from 'react-icons/fi';

export default function CustomerTable({ customers }) {
  return (
    <div className="overflow-x-auto">
      <table className="min-w-full divide-y divide-gray-200">
        <thead className="bg-gray-50">
          <tr>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined Date</th>
          </tr>
        </thead>
        <tbody className="bg-white divide-y divide-gray-200">
          {customers.length > 0 ? (
            customers.map((user) => (
              <tr key={user._id} className="hover:bg-gray-50 transition-colors">
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex items-center">
                    <div className="h-10 w-10 flex-shrink-0 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold">
                      {user.name?.charAt(0).toUpperCase() || 'U'}
                    </div>
                    <div className="ml-4">
                      <div className="text-sm font-medium text-gray-900">{user.name}</div>
                      <div className="text-xs text-gray-500">ID: {user._id.slice(-6).toUpperCase()}</div>
                    </div>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="flex flex-col gap-1 text-sm text-gray-500">
                    <div className="flex items-center gap-2">
                      <FiMail className="flex-shrink-0" size={14} />
                      <span>{user.email}</span>
                    </div>
                    {user.phone && (
                      <div className="flex items-center gap-2">
                        <FiPhone className="flex-shrink-0" size={14} />
                        <span>{user.phone}</span>
                      </div>
                    )}
                  </div>
                </td>
                <td className="px-6 py-4">
                  <div className="flex items-start gap-2 text-sm text-gray-500 max-w-xs">
                    <FiMapPin className="mt-1 flex-shrink-0" size={14} />
                    <span className="line-clamp-2">
                      {user.address ? (
                        `${user.address.street || ''}, ${user.address.city || ''}, ${user.address.state || ''}`
                      ) : (
                        'No address provided'
                      )}
                    </span>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {new Date(user.createdAt).toLocaleDateString()}
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="4" className="px-6 py-12 text-center text-gray-500 italic">
                No customers found.
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}
