import { NextResponse } from 'next/server';
import connectDB from '@/lib/mongodb';
import Order from '@/models/Order';
import { sendSMS } from '@/lib/sms';

// GET single order
export async function GET(request, { params }) {
  try {
    await connectDB();
    const order = await Order.findById(params.id)
      .populate('user', 'name email phone')
      .populate('items.product')
      .populate('vendor');
    
    if (!order) {
      return NextResponse.json(
        { success: false, error: 'Order not found' },
        { status: 404 }
      );
    }
    
    return NextResponse.json({
      success: true,
      data: order,
    });
  } catch (error) {
    return NextResponse.json(
      { success: false, error: error.message },
      { status: 500 }
    );
  }
}

// PUT update order status
export async function PUT(request, { params }) {
  try {
    await connectDB();
    const body = await request.json();
    
    const order = await Order.findByIdAndUpdate(
      params.id,
      { status: body.status },
      { new: true, runValidators: true }
    );
    
    if (!order) {
      return NextResponse.json(
        { success: false, error: 'Order not found' },
        { status: 404 }
      );
    }
    
    // Send SMS notification on status change
    if (order.deliveryAddress?.phone) {
      const message = `Order Update: Your order #${order.orderNumber} is now ${order.status.toUpperCase()}. You can check details here: ${process.env.NEXTAUTH_URL}/orders/${order._id}`;
      await sendSMS(order.deliveryAddress.phone, message);
    }
    
    return NextResponse.json({
      success: true,
      data: order,
    });
  } catch (error) {
    return NextResponse.json(
      { success: false, error: error.message },
      { status: 400 }
    );
  }
}

// DELETE order
export async function DELETE(request, { params }) {
  try {
    await connectDB();
    const order = await Order.findByIdAndDelete(params.id);
    
    if (!order) {
      return NextResponse.json(
        { success: false, error: 'Order not found' },
        { status: 404 }
      );
    }
    
    return NextResponse.json({
      success: true,
      message: 'Order deleted successfully',
    });
  } catch (error) {
    return NextResponse.json(
      { success: false, error: error.message },
      { status: 500 }
    );
  }
}
