import { NextResponse } from 'next/server';
import connectDB from '@/lib/mongodb';
import Order from '@/models/Order';

export async function POST(request) {
  try {
    const { orderId, status, gateway } = await request.json();

    if (!orderId || !status) {
      return NextResponse.json({ success: false, error: 'Missing parameters' }, { status: 400 });
    }

    await connectDB();
    
    const paymentStatusMapping = {
      success: 'Paid',
      failed: 'Failed',
      cancelled: 'Pending'
    };

    const updateData = {
      paymentStatus: paymentStatusMapping[status] || 'Pending',
    };

    // If payment is successful, maybe update some other fields
    if (status === 'success') {
      updateData.status = 'Processing';
    }

    const order = await Order.findByIdAndUpdate(
      orderId,
      updateData,
      { new: true }
    );

    if (!order) {
      return NextResponse.json({ success: false, error: 'Order not found' }, { status: 404 });
    }

    // TODO: Send confirmation SMS on success
    
    return NextResponse.json({
      success: true,
      message: `Payment status updated to ${updateData.paymentStatus}`,
      data: order
    });
  } catch (error) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
