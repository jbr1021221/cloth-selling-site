'use client';

import { useState, useEffect } from 'react';
import SMSForm from '@/components/admin/SMSForm';
import { FiMessageSquare, FiSend, FiClock, FiUsers } from 'react-icons/fi';
import toast from 'react-hot-toast';

export default function AdminSMSPage() {
  const [campaigns, setCampaigns] = useState([]);
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      const [campRes, custRes] = await Promise.all([
        fetch('/api/sms'),
        fetch('/api/users?role=customer')
      ]);
      
      const campData = await campRes.json();
      const custData = await custRes.json();
      
      if (campData.success) setCampaigns(campData.data);
      if (custData.success) setCustomers(custData.data.filter(c => !!c.phone));
    } catch (error) {
      toast.error('Failed to load data');
    } finally {
      setLoading(false);
    }
  };

  const handleSendSMS = async (payload) => {
    if (!window.confirm(`Are you sure you want to send this message to ${payload.recipientCount} people?`)) return;
    
    try {
      const res = await fetch('/api/sms', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      
      if (data.success) {
        toast.success('Campaign sent successfully!');
        fetchData();
      } else {
        toast.error(data.error || 'Failed to send campaign');
      }
    } catch (error) {
      toast.error('An error occurred while sending');
    }
  };

  return (
    <div className="space-y-8">
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold text-gray-800 flex items-center gap-3">
          <FiMessageSquare className="text-blue-600" />
          SMS Marketing
        </h1>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div className="lg:col-span-1">
          <SMSForm onSubmit={handleSendSMS} customers={customers} />
        </div>
        
        <div className="lg:col-span-2 space-y-4">
          <h2 className="text-xl font-bold text-gray-700 flex items-center gap-2">
            <FiClock /> Recent Campaigns
          </h2>
          
          <div className="space-y-4">
            {campaigns.length > 0 ? (
              campaigns.map((camp) => (
                <div key={camp._id} className="bg-white p-4 rounded-lg shadow-sm border hover:shadow-md transition">
                  <div className="flex justify-between items-start mb-2">
                    <span className="text-xs font-bold px-2 py-1 bg-green-100 text-green-700 rounded capitalize">
                      {camp.status}
                    </span>
                    <span className="text-xs text-gray-400">
                      {new Date(camp.createdAt).toLocaleString()}
                    </span>
                  </div>
                  <p className="text-sm text-gray-800 font-medium mb-3">"{camp.message}"</p>
                  <div className="flex items-center gap-4 text-xs text-gray-500 border-t pt-3">
                    <div className="flex items-center gap-1">
                      <FiUsers /> {camp.recipientCount} Recipients
                    </div>
                    <div className="flex items-center gap-1">
                      <FiSend /> Sent via Bulk Gateway
                    </div>
                  </div>
                </div>
              ))
            ) : (
              <div className="p-12 text-center text-gray-400 border-2 border-dashed rounded-lg">
                No campaigns sent yet.
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
