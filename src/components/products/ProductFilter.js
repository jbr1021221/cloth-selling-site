'use client';

import { useState } from 'react';

const categories = ['Shirt', 'T-Shirt', 'Pant', 'Jeans', 'Saree', 'Salwar', 'Kurti', 'Others'];
const sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

export default function ProductFilter({ onFilter }) {
  const [filters, setFilters] = useState({
    category: 'all',
    minPrice: '',
    maxPrice: '',
    size: '',
  });

  const handleChange = (name, value) => {
    const newFilters = { ...filters, [name]: value };
    setFilters(newFilters);
    onFilter(newFilters);
  };

  const handleReset = () => {
    const resetFilters = {
      category: 'all',
      minPrice: '',
      maxPrice: '',
      size: '',
    };
    setFilters(resetFilters);
    onFilter(resetFilters);
  };

  return (
    <div className="bg-white p-6 rounded-lg shadow-md sticky top-20">
      <div className="flex justify-between items-center mb-4">
        <h3 className="text-lg font-semibold">Filters</h3>
        <button
          onClick={handleReset}
          className="text-sm text-blue-600 hover:underline"
        >
          Reset
        </button>
      </div>

      {/* Category Filter */}
      <div className="mb-6">
        <label className="block text-sm font-medium mb-2">Category</label>
        <select
          value={filters.category}
          onChange={(e) => handleChange('category', e.target.value)}
          className="w-full border rounded-lg px-3 py-2"
        >
          <option value="all">All Categories</option>
          {categories.map((cat) => (
            <option key={cat} value={cat}>{cat}</option>
          ))}
        </select>
      </div>

      {/* Price Range */}
      <div className="mb-6">
        <label className="block text-sm font-medium mb-2">Price Range</label>
        <div className="flex gap-2">
          <input
            type="number"
            placeholder="Min"
            value={filters.minPrice}
            onChange={(e) => handleChange('minPrice', e.target.value)}
            className="w-full border rounded-lg px-3 py-2"
          />
          <input
            type="number"
            placeholder="Max"
            value={filters.maxPrice}
            onChange={(e) => handleChange('maxPrice', e.target.value)}
            className="w-full border rounded-lg px-3 py-2"
          />
        </div>
      </div>

      {/* Size Filter */}
      <div className="mb-6">
        <label className="block text-sm font-medium mb-2">Size</label>
        <select
          value={filters.size}
          onChange={(e) => handleChange('size', e.target.value)}
          className="w-full border rounded-lg px-3 py-2"
        >
          <option value="">All Sizes</option>
          {sizes.map((size) => (
            <option key={size} value={size}>{size}</option>
          ))}
        </select>
      </div>
    </div>
  );
}