'use client';

import { useState, useEffect } from 'react';
import VendorTable from '@/components/admin/VendorTable';
import VendorForm from '@/components/admin/VendorForm';
import { FiPlus, FiSearch, FiTruck } from 'react-icons/fi';
import toast from 'react-hot-toast';

export default function AdminVendorsPage() {
  const [vendors, setVendors] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingVendor, setEditingVendor] = useState(null);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    fetchVendors();
  }, []);

  const fetchVendors = async () => {
    try {
      setLoading(true);
      const res = await fetch('/api/vendors');
      const data = await res.json();
      if (data.success) {
        setVendors(data.data);
      }
    } catch (error) {
      toast.error('Failed to fetch vendors');
    } finally {
      setLoading(false);
    }
  };

  const handleCreate = async (formData) => {
    try {
      const res = await fetch('/api/vendors', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      const data = await res.json();
      if (data.success) {
        toast.success('Vendor added successfully');
        setShowForm(false);
        fetchVendors();
      } else {
        toast.error(data.error || 'Failed to add vendor');
      }
    } catch (error) {
      toast.error('An error occurred');
    }
  };

  const handleUpdate = async (formData) => {
    try {
      const res = await fetch(`/api/vendors/${formData._id}`, {
        method: 'PUT', // Assuming PUT exists or add it
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      const data = await res.json();
      if (data.success) {
        toast.success('Vendor updated successfully');
        setShowForm(false);
        setEditingVendor(null);
        fetchVendors();
      } else {
        toast.error(data.error || 'Failed to update vendor');
      }
    } catch (error) {
      toast.error('An error occurred');
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Are you sure you want to delete this vendor?')) return;
    try {
      const res = await fetch(`/api/vendors/${id}`, {
        method: 'DELETE',
      });
      const data = await res.json();
      if (data.success) {
        toast.success('Vendor deleted successfully');
        fetchVendors();
      }
    } catch (error) {
      toast.error('An error occurred');
    }
  };

  const filteredVendors = vendors.filter(v => 
    v.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    v.contactPerson.toLowerCase().includes(searchTerm.toLowerCase()) ||
    v.phone.includes(searchTerm)
  );

  return (
    <div className="space-y-6">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 className="text-3xl font-bold text-gray-800 flex items-center gap-3">
          <FiTruck className="text-blue-600" />
          Vendor Management
        </h1>
        <button
          onClick={() => {
            setEditingVendor(null);
            setShowForm(true);
          }}
          className="flex items-center justify-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
        >
          <FiPlus size={20} />
          Add New Vendor
        </button>
      </div>

      <div className="relative">
        <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
          <FiSearch size={18} />
        </span>
        <input
          type="text"
          placeholder="Search vendors by name, contact person or phone..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
        />
      </div>

      {showForm ? (
        <div className="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
          <div className="w-full max-w-2xl">
            <VendorForm
              vendor={editingVendor}
              onSubmit={editingVendor ? handleUpdate : handleCreate}
              onCancel={() => {
                setShowForm(false);
                setEditingVendor(null);
              }}
            />
          </div>
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow-md">
          {loading ? (
            <div className="p-8 text-center text-gray-500 italic">Loading vendor list...</div>
          ) : (
            <VendorTable
              vendors={filteredVendors}
              onEdit={(v) => {
                setEditingVendor(v);
                setShowForm(true);
              }}
              onDelete={handleDelete}
            />
          )}
        </div>
      )}
    </div>
  );
}
