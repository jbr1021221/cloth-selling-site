'use client';

import { useState, useEffect } from 'react';
import CustomerTable from '@/components/admin/CustomerTable';
import { FiSearch, FiUsers } from 'react-icons/fi';
import toast from 'react-hot-toast';

export default function AdminCustomersPage() {
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    fetchCustomers();
  }, []);

  const fetchCustomers = async () => {
    try {
      setLoading(true);
      const res = await fetch('/api/users?role=customer');
      const data = await res.json();
      if (data.success) {
        setCustomers(data.data);
      }
    } catch (error) {
      toast.error('Failed to fetch customers');
    } finally {
      setLoading(false);
    }
  };

  const filteredCustomers = customers.filter(c => 
    c.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    c.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
    (c.phone && c.phone.includes(searchTerm))
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold text-gray-800 flex items-center gap-3">
          <FiUsers className="text-blue-600" />
          Customer Management
        </h1>
        <div className="bg-blue-100 text-blue-700 px-4 py-1 rounded-full text-sm font-semibold">
          Total: {customers.length}
        </div>
      </div>

      <div className="relative">
        <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
          <FiSearch size={18} />
        </span>
        <input
          type="text"
          placeholder="Search customers by name, email or phone..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
        />
      </div>

      <div className="bg-white rounded-lg shadow-md">
        {loading ? (
          <div className="p-8 text-center text-gray-500 italic">Loading customer list...</div>
        ) : (
          <CustomerTable customers={filteredCustomers} />
        )}
      </div>
    </div>
  );
}
