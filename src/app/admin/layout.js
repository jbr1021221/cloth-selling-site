// Admin Layout - Wraps all admin pages with sidebar
import AdminSidebar from '@/components/layout/AdminSidebar';

export const metadata = {
  title: 'Admin Panel - ClothStore',
  description: 'Manage your online store',
};

export default function AdminLayout({ children }) {
  return (
    <div className="flex min-h-screen bg-gray-100">
      <AdminSidebar />
      <main className="flex-1 overflow-auto">
        {children}
      </main>
    </div>
  );
}
