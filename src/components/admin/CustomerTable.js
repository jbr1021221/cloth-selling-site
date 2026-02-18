import { FiMail, FiPhone, FiMapPin, FiTrash2, FiShield } from 'react-icons/fi';

export default function CustomerTable({ customers, onRoleUpdate, onDelete }) {
  return (
    <div className="overflow-x-auto">
      <table className="min-w-full divide-y divide-gray-200">
        <thead className="bg-gray-50">
          <tr>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined Date</th>
            <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody className="bg-white divide-y divide-gray-200">
          {customers.length > 0 ? (
            customers.map((user) => (
              <tr key={user._id} className="hover:bg-gray-50 transition-colors">
                <td className="px-6 py-4">
                  <div className="flex items-center">
                    <div className="h-10 w-10 flex-shrink-0 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold">
                      {user.name?.charAt(0).toUpperCase() || 'U'}
                    </div>
                    <div className="ml-4">
                      <div className="text-sm font-medium text-gray-900">{user.name}</div>
                      <div className="text-xs text-gray-500 flex items-center gap-1">
                        {user.role === 'admin' && <FiShield className="text-purple-500" />}
                        ID: {user._id.slice(-6).toUpperCase()}
                      </div>
                    </div>
                  </div>
                </td>
                <td className="px-6 py-4">
                  <div className="flex flex-col gap-1 text-xs text-gray-500">
                    <div className="flex items-center gap-2">
                      <FiMail className="flex-shrink-0" size={14} />
                      <span>{user.email}</span>
                    </div>
                    {user.phone && (
                      <div className="flex items-center gap-2">
                        <FiPhone className="flex-shrink-0" size={14} />
                        <span>{user.phone}</span>
                      </div>
                    )}
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                   <select 
                    value={user.role}
                    onChange={(e) => onRoleUpdate(user._id, e.target.value)}
                    className={`text-xs font-bold rounded px-2 py-1 outline-none border ${
                      user.role === 'admin' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-gray-50 text-gray-700 border-gray-200'
                    }`}
                   >
                     <option value="customer">Customer</option>
                     <option value="admin">Admin</option>
                     <option value="vendor">Vendor</option>
                   </select>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {new Date(user.createdAt).toLocaleDateString()}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                   <button 
                    onClick={() => onDelete(user._id)}
                    className="text-red-600 hover:text-red-900 p-2 bg-red-50 rounded-full"
                   >
                     <FiTrash2 size={16} />
                   </button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="5" className="px-6 py-12 text-center text-gray-500 italic">
                No users found.
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}
