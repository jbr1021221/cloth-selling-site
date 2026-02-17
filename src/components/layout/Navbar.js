'use client';

import Link from 'next/link';
import { FiShoppingCart, FiUser, FiMenu } from 'react-icons/fi';
import useCartStore from '@/store/cartStore';
import { useState } from 'react';

export default function Navbar() {
  const itemCount = useCartStore((state) => state.getItemCount());
  const [menuOpen, setMenuOpen] = useState(false);

  return (
    <nav className="bg-white shadow-md sticky top-0 z-50">
      <div className="container mx-auto px-4">
        <div className="flex justify-between items-center h-16">
          {/* Logo */}
          <Link href="/" className="text-2xl font-bold text-blue-600">
            ClothStore
          </Link>

          {/* Desktop Menu */}
          <div className="hidden md:flex items-center space-x-8">
            <Link href="/" className="text-gray-700 hover:text-blue-600">
              Home
            </Link>
            <Link href="/products" className="text-gray-700 hover:text-blue-600">
              Products
            </Link>
            <Link href="/cart" className="relative text-gray-700 hover:text-blue-600">
              <FiShoppingCart size={24} />
              {itemCount > 0 && (
                <span className="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                  {itemCount}
                </span>
              )}
            </Link>
            <Link href="/login" className="text-gray-700 hover:text-blue-600">
              <FiUser size={24} />
            </Link>
          </div>

          {/* Mobile Menu Button */}
          <button
            className="md:hidden"
            onClick={() => setMenuOpen(!menuOpen)}
          >
            <FiMenu size={24} />
          </button>
        </div>

        {/* Mobile Menu */}
        {menuOpen && (
          <div className="md:hidden pb-4">
            <Link href="/" className="block py-2 text-gray-700">
              Home
            </Link>
            <Link href="/products" className="block py-2 text-gray-700">
              Products
            </Link>
            <Link href="/cart" className="block py-2 text-gray-700">
              Cart ({itemCount})
            </Link>
            <Link href="/login" className="block py-2 text-gray-700">
              Login
            </Link>
          </div>
        )}
      </div>
    </nav>
  );
}