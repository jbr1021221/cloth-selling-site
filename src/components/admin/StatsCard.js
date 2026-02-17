// Statistics Card Component - Display dashboard metrics
export default function StatsCard({ title, value, icon, trend, color = 'blue' }) {
  return (
    <div className="bg-white rounded-lg shadow-md p-6">
      <h3 className="text-gray-600 text-sm mb-2">{title || 'Stat Title'}</h3>
      <p className="text-2xl font-bold">{value || '0'}</p>
    </div>
  );
}
