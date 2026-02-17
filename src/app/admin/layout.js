'use client';

import AdminSidebar from '@/components/layout/AdminSidebar';

export default function AdminLayout({ children }) {
  return (
    <div className="flex h-screen bg-gray-100 dark:bg-gray-900">
      <AdminSidebar />
      <div className="flex-1 flex flex-col overflow-hidden">
        <header className="flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 shadow">
          <div className="flex items-center">
            {/* Mobile menu toggle could go here */}
            <h1 className="text-2xl font-semibold text-gray-800 dark:text-gray-200">
              Admin Panel
            </h1>
          </div>
          <div className="flex items-center">
            {/* Additional header items like notifications or user profile could go here */}
          </div>
        </header>

        <main className="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
          {children}
        </main>
      </div>
    </div>
  );
}
