'use client';

import { FiEdit2, FiTrash2, FiUser, FiPhone, FiMail, FiCheckCircle, FiXCircle } from 'react-icons/fi';

export default function VendorTable({ vendors, onEdit, onDelete }) {
  return (
    <div className="overflow-x-auto">
      <table className="min-w-full divide-y divide-gray-200">
        <thead className="bg-gray-50">
          <tr>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor Info</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody className="bg-white divide-y divide-gray-200">
          {vendors.length > 0 ? (
            vendors.map((vendor) => (
              <tr key={vendor._id} className="hover:bg-gray-50 transition-colors">
                <td className="px-6 py-4">
                  <div className="text-sm font-bold text-gray-900">{vendor.name}</div>
                  <div className="text-xs text-gray-500 flex items-center gap-1 mt-1">
                    <FiUser size={12} />
                    {vendor.contactPerson}
                  </div>
                </td>
                <td className="px-6 py-4">
                  <div className="flex flex-col gap-1 text-xs text-gray-500">
                    <div className="flex items-center gap-2">
                       <FiPhone size={12} />
                       <span>{vendor.phone}</span>
                    </div>
                    {vendor.email && (
                      <div className="flex items-center gap-2 text-blue-600">
                        <FiMail size={12} />
                        <span>{vendor.email}</span>
                      </div>
                    )}
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">
                  {vendor.commissionRate}%
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                   <div className="flex items-center gap-2">
                    {vendor.isActive ? (
                      <span className="flex items-center gap-1 text-green-600 text-xs font-bold px-2 py-1 bg-green-50 rounded-full">
                        <FiCheckCircle /> Active
                      </span>
                    ) : (
                      <span className="flex items-center gap-1 text-red-600 text-xs font-bold px-2 py-1 bg-red-50 rounded-full">
                        <FiXCircle /> Inactive
                      </span>
                    )}
                   </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div className="flex justify-end gap-2">
                    <button 
                      onClick={() => onEdit(vendor)}
                      className="text-blue-600 hover:text-blue-900 p-1 bg-blue-50 rounded"
                    >
                      <FiEdit2 size={16} />
                    </button>
                    <button 
                      onClick={() => onDelete(vendor._id)}
                      className="text-red-600 hover:text-red-900 p-1 bg-red-50 rounded"
                    >
                      <FiTrash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="5" className="px-6 py-12 text-center text-gray-500 italic">
                No vendors found.
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}
