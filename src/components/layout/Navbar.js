'use client';

import Link from 'next/link';
import { FiShoppingCart, FiUser, FiMenu, FiLogOut, FiPackage } from 'react-icons/fi';
import useCartStore from '@/store/cartStore';
import { useState } from 'react';
import { useSession, signOut } from 'next-auth/react';

export default function Navbar() {
  const itemCount = useCartStore((state) => state.getItemCount());
  const [menuOpen, setMenuOpen] = useState(false);
  const [userMenuOpen, setUserMenuOpen] = useState(false);
  const { data: session, status } = useSession();

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
            
            {/* User Menu - Desktop */}
            {status === 'loading' ? (
              <div className="w-8 h-8 border-2 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>
            ) : session ? (
              <div className="relative">
                <button
                  onClick={() => setUserMenuOpen(!userMenuOpen)}
                  className="flex items-center gap-2 text-gray-700 hover:text-blue-600"
                >
                  <FiUser size={24} />
                  <span className="text-sm font-medium">{session.user.name}</span>
                </button>
                
                {userMenuOpen && (
                  <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 border">
                    <div className="px-4 py-2 border-b">
                      <p className="text-xs text-gray-500">Signed in as</p>
                      <p className="text-sm font-medium truncate">{session.user.email}</p>
                    </div>
                    <Link
                      href="/orders"
                      className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2"
                      onClick={() => setUserMenuOpen(false)}
                    >
                      <FiPackage size={16} />
                      My Orders
                    </Link>
                    {session.user.role === 'admin' && (
                      <Link
                        href="/admin/dashboard"
                        className="block px-4 py-2 text-sm text-purple-600 hover:bg-purple-50 font-semibold flex items-center gap-2"
                        onClick={() => setUserMenuOpen(false)}
                      >
                        <FiUser size={16} />
                        Admin Panel
                      </Link>
                    )}
                    <button
                      onClick={() => {
                        signOut({ callbackUrl: '/' });
                        setUserMenuOpen(false);
                      }}
                      className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2"
                    >
                      <FiLogOut size={16} />
                      Sign Out
                    </button>
                  </div>
                )}
              </div>
            ) : (
              <Link href="/login" className="text-gray-700 hover:text-blue-600 flex items-center gap-2">
                <FiUser size={24} />
                <span className="text-sm">Login</span>
              </Link>
            )}
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
          <div className="md:hidden pb-4 border-t">
            <Link href="/" className="block py-2 text-gray-700" onClick={() => setMenuOpen(false)}>
              Home
            </Link>
            <Link href="/products" className="block py-2 text-gray-700" onClick={() => setMenuOpen(false)}>
              Products
            </Link>
            <Link href="/cart" className="block py-2 text-gray-700" onClick={() => setMenuOpen(false)}>
              Cart ({itemCount})
            </Link>
            {session ? (
              <>
                <Link href="/orders" className="block py-2 text-gray-700" onClick={() => setMenuOpen(false)}>
                  My Orders
                </Link>
                {session.user.role === 'admin' && (
                  <Link href="/admin/dashboard" className="block py-2 text-purple-600 font-semibold" onClick={() => setMenuOpen(false)}>
                    Admin Panel
                  </Link>
                )}
                <button
                  onClick={() => {
                    signOut({ callbackUrl: '/' });
                    setMenuOpen(false);
                  }}
                  className="block w-full text-left py-2 text-red-600"
                >
                  Sign Out
                </button>
              </>
            ) : (
              <Link href="/login" className="block py-2 text-gray-700" onClick={() => setMenuOpen(false)}>
                Login
              </Link>
            )}
          </div>
        )}
      </div>
    </nav>
  );
}