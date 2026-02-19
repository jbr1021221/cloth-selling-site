'use client';

import { useState } from 'react';
import ProductCard from './ProductCard';
import ProductFilter from './ProductFilter';

export default function ProductGrid({ products }) {
  const [filteredProducts, setFilteredProducts] = useState(products);
  const [sortBy, setSortBy] = useState('newest');

  const handleSort = (value) => {
    setSortBy(value);
    let sorted = [...filteredProducts];

    switch (value) {
      case 'price-low':
        sorted.sort((a, b) => ((a.discountPrice || a.discount_price || a.price) - (b.discountPrice || b.discount_price || b.price)));
        break;
      case 'price-high':
        sorted.sort((a, b) => ((b.discountPrice || b.discount_price || b.price) - (a.discountPrice || a.discount_price || a.price)));
        break;
      case 'newest':
        sorted.sort((a, b) => new Date(b.createdAt || b.created_at) - new Date(a.createdAt || a.created_at));
        break;
      default:
        break;
    }

    setFilteredProducts(sorted);
  };

  const handleFilter = (filters) => {
    let filtered = [...products];

    // Filter by category
    if (filters.category && filters.category !== 'all') {
      filtered = filtered.filter(p => p.category === filters.category);
    }

    // Filter by price range
    if (filters.minPrice) {
      filtered = filtered.filter(p => (p.discountPrice || p.discount_price || p.price) >= filters.minPrice);
    }
    if (filters.maxPrice) {
      filtered = filtered.filter(p => (p.discountPrice || p.discount_price || p.price) <= filters.maxPrice);
    }

    // Filter by size
    if (filters.size) {
      filtered = filtered.filter(p => p.sizes.includes(filters.size));
    }

    setFilteredProducts(filtered);
  };

  return (
    <div className="flex flex-col md:flex-row gap-6">
      {/* Filters Sidebar */}
      <div className="w-full md:w-64 flex-shrink-0">
        <ProductFilter onFilter={handleFilter} />
      </div>

      {/* Products Grid */}
      <div className="flex-1">
        {/* Sort Options */}
        <div className="flex justify-between items-center mb-6">
          <p className="text-gray-600">
            {filteredProducts.length} Products
          </p>

          <select
            value={sortBy}
            onChange={(e) => handleSort(e.target.value)}
            className="border rounded-lg px-4 py-2"
          >
            <option value="newest">Newest</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
          </select>
        </div>

        {/* Products */}
        {filteredProducts.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredProducts.map((product) => (
              <ProductCard key={product._id || product.id} product={product} />
            ))}
          </div>
        ) : (
          <div className="text-center py-12">
            <p className="text-gray-500 text-lg">No products found</p>
          </div>
        )}
      </div>
    </div>
  );
}