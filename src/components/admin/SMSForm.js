'use client';

import { useState } from 'react';

export default function SMSForm({ onSubmit, customers = [] }) {
  const [formData, setFormData] = useState({
    message: '',
    recipientType: 'all', // 'all', 'specific'
    selectedRecipients: [],
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    
    let recipients = [];
    if (formData.recipientType === 'all') {
      recipients = customers.map(c => ({ phone: c.phone, name: c.name }));
    } else {
      recipients = formData.selectedRecipients;
    }
    
    if (recipients.length === 0) {
      alert('No recipients selected');
      return;
    }
    
    onSubmit({
      message: formData.message,
      recipients,
      recipientCount: recipients.length
    });
  };

  return (
    <div className="bg-white p-6 rounded-lg shadow-md border">
      <h2 className="text-xl font-bold mb-4">Create SMS Campaign</h2>
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Select Audience</label>
          <div className="flex gap-4">
            <label className="flex items-center gap-2">
              <input 
                type="radio" 
                name="recipientType" 
                value="all" 
                checked={formData.recipientType === 'all'}
                onChange={(e) => setFormData({...formData, recipientType: e.target.value})}
              />
              <span className="text-sm">All Customers ({customers.length})</span>
            </label>
            <label className="flex items-center gap-2">
              <input 
                type="radio" 
                name="recipientType" 
                value="custom" 
                disabled
                className="opacity-50"
              />
              <span className="text-sm text-gray-400">Custom Group (Feature coming soon)</span>
            </label>
          </div>
        </div>
        
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Message Content
            <span className={`float-right text-xs ${formData.message.length > 160 ? 'text-red-500' : 'text-gray-400'}`}>
              {formData.message.length}/160
            </span>
          </label>
          <textarea
            required
            rows="4"
            maxLength="160"
            className="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-blue-500 focus:border-blue-500"
            placeholder="Type your message here..."
            value={formData.message}
            onChange={(e) => setFormData({...formData, message: e.target.value})}
          ></textarea>
          <p className="mt-1 text-xs text-gray-500">
            Standard SMS limit is 160 characters.
          </p>
        </div>
        
        <button
          type="submit"
          className="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 font-bold transition"
        >
          Send SMS Campaign Now
        </button>
      </form>
    </div>
  );
}
