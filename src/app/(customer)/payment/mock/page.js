'use client';

import { Suspense, useEffect, useState } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import toast from 'react-hot-toast';

function MockPaymentContent() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const gateway = searchParams.get('gateway');
  const orderId = searchParams.get('orderId');
  const amount = searchParams.get('amount');
  
  const [loading, setLoading] = useState(false);

  const handlePayment = async (status) => {
    setLoading(true);
    try {
      const res = await fetch('/api/payment/verify', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ orderId, status, gateway }),
      });
      
      const data = await res.json();
      
      if (data.success) {
        toast.success(status === 'success' ? 'Payment Successful!' : 'Payment Failed/Cancelled');
        router.push(`/orders/${orderId}`);
      } else {
        throw new Error(data.error);
      }
    } catch (error) {
      toast.error(error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100 p-4">
      <div className="bg-white p-8 rounded-xl shadow-2xl max-w-md w-full text-center">
        <h1 className="text-2xl font-bold mb-4 uppercase tracking-widest text-blue-600">
          {gateway} Payment Gateway
        </h1>
        <div className="mb-8 p-6 bg-blue-50 rounded-lg">
          <p className="text-gray-600 mb-1">Total Amount to Pay</p>
          <p className="text-4xl font-extrabold text-blue-800">৳{amount}</p>
        </div>
        
        <p className="mb-8 text-gray-500 text-sm">
          This is a simulated payment gateway for demonstration purposes.
        </p>
        
        <div className="space-y-4">
          <button
            onClick={() => handlePayment('success')}
            disabled={loading}
            className="w-full bg-green-600 text-white py-4 rounded-lg font-bold hover:bg-green-700 transition transform hover:scale-105"
          >
            {loading ? 'Processing...' : 'Simulate SUCCESS'}
          </button>
          
          <button
            onClick={() => handlePayment('failed')}
            disabled={loading}
            className="w-full bg-red-600 text-white py-4 rounded-lg font-bold hover:bg-red-700 transition transform hover:scale-105"
          >
            Simulate FAILURE
          </button>
          
          <button
            onClick={() => router.push(`/orders/${orderId}`)}
            className="w-full bg-gray-200 text-gray-700 py-4 rounded-lg font-bold hover:bg-gray-300 transition"
          >
            CANCEL PAYMENT
          </button>
        </div>
      </div>
    </div>
  );
}

export default function MockPaymentPage() {
  return (
    <Suspense fallback={<div>Loading payment gateway...</div>}>
      <MockPaymentContent />
    </Suspense>
  );
}
