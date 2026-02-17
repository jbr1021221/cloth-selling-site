'use client';

import { useState, useEffect } from 'react';
import ProductTable from '@/components/admin/ProductTable';
import ProductForm from '@/components/admin/ProductForm';
import { FiPlus, FiSearch } from 'react-icons/fi';
import toast from 'react-hot-toast';

export default function AdminProductsPage() {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingProduct, setEditingProduct] = useState(null);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    try {
      const res = await fetch('/api/products');
      const data = await res.json();
      if (data.success) {
        setProducts(data.data);
      }
    } catch (error) {
      toast.error('Failed to fetch products');
    } finally {
      setLoading(false);
    }
  };

  const handleCreate = async (formData) => {
    try {
      const res = await fetch('/api/products', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      const data = await res.json();
      if (data.success) {
        toast.success('Product created successfully');
        setShowForm(false);
        fetchProducts();
      } else {
        toast.error(data.error || 'Failed to create product');
      }
    } catch (error) {
      toast.error('An error occurred');
    }
  };

  const handleUpdate = async (formData) => {
    try {
      const res = await fetch(`/api/products/${formData._id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      const data = await res.json();
      if (data.success) {
        toast.success('Product updated successfully');
        setShowForm(false);
        setEditingProduct(null);
        fetchProducts();
      } else {
        toast.error(data.error || 'Failed to update product');
      }
    } catch (error) {
      toast.error('An error occurred');
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Are you sure you want to delete this product?')) return;
    
    try {
      const res = await fetch(`/api/products/${id}`, {
        method: 'DELETE',
      });
      const data = await res.json();
      if (data.success) {
        toast.success('Product deleted successfully');
        fetchProducts();
      } else {
        toast.error(data.error || 'Failed to delete product');
      }
    } catch (error) {
      toast.error('An error occurred');
    }
  };

  const filteredProducts = products.filter(p => 
    p.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    p.category.toLowerCase().includes(searchTerm.toLowerCase())
  );

  if (loading && products.length === 0) {
    return <div className="p-8 text-center text-gray-500">Loading products...</div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h1 className="text-3xl font-bold text-gray-800">Products ({products.length})</h1>
        <button
          onClick={() => {
            setEditingProduct(null);
            setShowForm(true);
          }}
          className="flex items-center justify-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
        >
          <FiPlus size={20} />
          Add New Product
        </button>
      </div>

      <div className="relative">
        <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
          <FiSearch size={18} />
        </span>
        <input
          type="text"
          placeholder="Search products by name or category..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
        />
      </div>

      {showForm ? (
        <div className="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
          <div className="w-full max-w-4xl">
            <ProductForm
              product={editingProduct}
              onSubmit={editingProduct ? handleUpdate : handleCreate}
              onCancel={() => {
                setShowForm(false);
                setEditingProduct(null);
              }}
            />
          </div>
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow-md">
          <ProductTable
            products={filteredProducts}
            onEdit={(p) => {
              setEditingProduct(p);
              setShowForm(true);
            }}
            onDelete={handleDelete}
          />
        </div>
      )}
    </div>
  );
}
