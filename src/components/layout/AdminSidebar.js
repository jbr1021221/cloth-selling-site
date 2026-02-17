'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { 
  FiHome, 
  FiPackage, 
  FiShoppingBag, 
  FiUsers, 
  FiTruck, 
  FiMessageSquare,
  FiLogOut
} from 'react-icons/fi';
import { signOut } from 'next-auth/react';

const navigation = [
  { name: 'Dashboard', href: '/admin/dashboard', icon: FiHome },
  { name: 'Products', href: '/admin/products', icon: FiPackage },
  { name: 'Orders', href: '/admin/orders', icon: FiShoppingBag },
  { name: 'Customers', href: '/admin/customers', icon: FiUsers },
  { name: 'Vendors', href: '/admin/vendors', icon: FiTruck },
  { name: 'SMS Campaigns', href: '/admin/sms', icon: FiMessageSquare },
];

export default function AdminSidebar() {
  const pathname = usePathname();

  return (
    <div className="flex flex-col w-64 bg-gray-900 text-white min-h-screen">
      <div className="flex items-center justify-center h-16 border-b border-gray-800">
        <span className="text-xl font-bold text-white">ClothStore Admin</span>
      </div>

      <nav className="flex-1 px-4 py-6 space-y-1">
        {navigation.map((item) => {
          const isActive = pathname === item.href;
          return (
            <Link
              key={item.name}
              href={item.href}
              className={`flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors ${
                isActive
                  ? 'bg-blue-600 text-white'
                  : 'text-gray-300 hover:bg-gray-800 hover:text-white'
              }`}
            >
              <item.icon className="mr-3 h-5 w-5" />
              {item.name}
            </Link>
          );
        })}
      </nav>

      <div className="p-4 border-t border-gray-800">
        <button
          onClick={() => signOut({ callbackUrl: '/login' })}
          className="flex items-center w-full px-4 py-2 text-sm font-medium text-red-400 hover:bg-gray-800 hover:text-red-300 rounded-lg transition-colors"
        >
          <FiLogOut className="mr-3 h-5 w-5" />
          Sign Out
        </button>
      </div>
    </div>
  );
}
