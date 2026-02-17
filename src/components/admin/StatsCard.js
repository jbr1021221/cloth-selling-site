'use client';

export default function StatsCard({ title, value, icon: Icon, trend, color, description }) {
  const getColors = () => {
    switch(color) {
      case 'purple': return 'bg-purple-100 text-purple-600';
      case 'blue': return 'bg-blue-100 text-blue-600';
      case 'green': return 'bg-green-100 text-green-600';
      case 'red': return 'bg-red-100 text-red-600';
      default: return 'bg-gray-100 text-gray-600';
    }
  };

  return (
    <div className="bg-white rounded-lg shadow-md p-6">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-gray-500 text-sm font-medium uppercase tracking-wider">{title}</p>
          <div className="mt-2 text-3xl font-bold text-gray-800">{value}</div>
        </div>
        <div className={`p-4 rounded-full ${getColors()}`}>
          <Icon className="w-6 h-6" />
        </div>
      </div>
      {description && (
        <div className="mt-4 flex items-center text-sm">
          {trend && (
            <span className={`font-semibold mr-2 ${
              trend > 0 ? 'text-green-600' : 'text-red-600'
            }`}>
              {trend > 0 ? '+' : ''}{trend}%
            </span>
          )}
          <span className="text-gray-400">{description}</span>
        </div>
      )}
    </div>
  );
}
