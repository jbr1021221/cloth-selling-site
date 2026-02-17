'use client';

import { useState, useEffect } from 'react';
import { FiX } from 'react-icons/fi';

export default function VendorForm({ vendor, onSubmit, onCancel }) {
  const [formData, setFormData] = useState({
    name: '',
    contactPerson: '',
    phone: '',
    email: '',
    address: '',
    commissionRate: 0,
    isActive: true,
  });

  useEffect(() => {
    if (vendor) {
      setFormData(vendor);
    }
  }, [vendor]);

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData({
      ...formData,
      [name]: type === 'checkbox' ? checked : value
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSubmit(formData);
  };

  return (
    <div className="bg-white p-6 rounded-lg shadow-lg relative">
      <button 
        onClick={onCancel}
        className="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
      >
        <FiX size={24} />
      </button>
      
      <h2 className="text-2xl font-bold mb-6">{vendor ? 'Edit Vendor' : 'Add New Vendor'}</h2>
      
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700">Vendor Business Name</label>
          <input
            type="text"
            name="name"
            required
            value={formData.name}
            onChange={handleChange}
            className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
          />
        </div>
        
        <div className="grid grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700">Contact Person</label>
            <input
              type="text"
              name="contactPerson"
              required
              value={formData.contactPerson}
              onChange={handleChange}
              className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700">Phone Number</label>
            <input
              type="text"
              name="phone"
              required
              value={formData.phone}
              onChange={handleChange}
              className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
            />
          </div>
        </div>
        
        <div>
          <label className="block text-sm font-medium text-gray-700">Email Address (Optional)</label>
          <input
            type="email"
            name="email"
            value={formData.email}
            onChange={handleChange}
            className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
          />
        </div>
        
        <div>
          <label className="block text-sm font-medium text-gray-700">Office/Store Address</label>
          <textarea
            name="address"
            required
            rows="2"
            value={formData.address}
            onChange={handleChange}
            className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
          ></textarea>
        </div>
        
        <div className="grid grid-cols-2 gap-4 items-center">
          <div>
            <label className="block text-sm font-medium text-gray-700">Commission Rate (%)</label>
            <input
              type="number"
              name="commissionRate"
              value={formData.commissionRate}
              onChange={handleChange}
              className="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
            />
          </div>
          <div className="flex items-center gap-2 mt-6">
            <input
              type="checkbox"
              id="isActive"
              name="isActive"
              checked={formData.isActive}
              onChange={handleChange}
              className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            />
            <label htmlFor="isActive" className="text-sm font-medium text-gray-700">Is Active Vendor</label>
          </div>
        </div>
        
        <div className="flex justify-end gap-3 pt-4 border-t">
          <button
            type="button"
            onClick={onCancel}
            className="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            {vendor ? 'Update Vendor' : 'Add Vendor'}
          </button>
        </div>
      </form>
    </div>
  );
}
