import { NextResponse } from 'next/server';
import { getServerSession } from 'next-auth';
import { authOptions } from '@/app/api/auth/[...nextauth]/route';
import connectDB from '@/lib/mongodb';
import Order from '@/models/Order';

export async function GET(request) {
  try {
    const session = await getServerSession(authOptions);
    if (!session) {
      return NextResponse.json({ success: false, error: 'Unauthorized' }, { status: 401 });
    }

    const { searchParams } = new URL(request.url);
    const orderId = searchParams.get('orderId');

    if (!orderId) {
      return NextResponse.json({ success: false, error: 'Order ID is required' }, { status: 400 });
    }

    await connectDB();
    const order = await Order.findById(orderId);

    if (!order) {
      return NextResponse.json({ success: false, error: 'Order not found' }, { status: 404 });
    }

    if (order.user.toString() !== session.user.id) {
       return NextResponse.json({ success: false, error: 'Unauthorized' }, { status: 401 });
    }

    // Logic for different payment gateways
    if (order.paymentMethod === 'bKash') {
      // Mock bKash initiation
      // In real: call bKash create payment API
      return NextResponse.json({
        success: true,
        gateway: 'bKash',
        redirectUrl: `/payment/mock?gateway=bkash&orderId=${orderId}&amount=${order.finalAmount}`
      });
    } else if (order.paymentMethod === 'Card' || order.paymentMethod === 'ssl_commerz') {
      // Mock SSLCommerz initiation
      return NextResponse.json({
        success: true,
        gateway: 'SSLCommerz',
        redirectUrl: `/payment/mock?gateway=sslcommerz&orderId=${orderId}&amount=${order.finalAmount}`
      });
    }

    return NextResponse.json({ success: false, error: 'Invalid payment method' }, { status: 400 });
  } catch (error) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
