'use client';

import { FiEdit2, FiTrash2, FiEye } from 'react-icons/fi';
import Image from 'next/image';

export default function ProductTable({ products, onEdit, onDelete }) {
  return (
    <div className="overflow-x-auto">
      <table className="min-w-full divide-y divide-gray-200">
        <thead className="bg-gray-50">
          <tr>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody className="bg-white divide-y divide-gray-200">
          {products.map((product) => (
            <tr key={product._id} className="hover:bg-gray-50 transition-colors">
              <td className="px-6 py-4 whitespace-nowrap">
                <div className="flex items-center">
                  <div className="flex-shrink-0 h-10 w-10 relative">
                    <Image
                      src={product.images?.[0] || '/placeholder.jpg'}
                      alt={product.name}
                      fill
                      className="rounded-md object-cover"
                    />
                  </div>
                  <div className="ml-4">
                    <div className="text-sm font-medium text-gray-900 line-clamp-1">{product.name}</div>
                    <div className="text-xs text-gray-500">ID: {product._id.slice(-6).toUpperCase()}</div>
                  </div>
                </div>
              </td>
              <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {product.category}
              </td>
              <td className="px-6 py-4 whitespace-nowrap">
                <div className="text-sm text-gray-900 font-medium">৳{product.discountPrice || product.price}</div>
                {product.discountPrice && (
                  <div className="text-xs text-gray-400 line-through">৳{product.price}</div>
                )}
              </td>
              <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span className={product.stock < 10 ? 'text-red-500 font-bold' : ''}>
                  {product.stock}
                </span>
              </td>
              <td className="px-6 py-4 whitespace-nowrap">
                <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                  product.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                }`}>
                  {product.stock > 0 ? 'Active' : 'Out of Stock'}
                </span>
              </td>
              <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div className="flex justify-end gap-2">
                  <button 
                    onClick={() => onEdit(product)}
                    className="text-blue-600 hover:text-blue-900 p-1"
                    title="Edit"
                  >
                    <FiEdit2 size={18} />
                  </button>
                  <button 
                    onClick={() => onDelete(product._id)}
                    className="text-red-600 hover:text-red-900 p-1"
                    title="Delete"
                  >
                    <FiTrash2 size={18} />
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
