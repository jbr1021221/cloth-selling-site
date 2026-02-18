import { NextResponse } from 'next/server';
import { getServerSession } from 'next-auth';
import { authOptions } from '@/app/api/auth/[...nextauth]/route';
import connectDB from '@/lib/mongodb';
import Order from '@/models/Order';
import { sendSMS } from '@/lib/sms';
import { sendEmail } from '@/lib/email';

// GET all orders
export async function GET(request) {
  try {
    const session = await getServerSession(authOptions);
    if (!session) {
      return NextResponse.json({ success: false, error: 'Unauthorized' }, { status: 401 });
    }

    await connectDB();
    
    const { searchParams } = new URL(request.url);
    const userId = session.user.role === 'admin' ? searchParams.get('userId') : session.user.id;
    const status = searchParams.get('status');
    
    let query = {};
    
    if (userId) {
      query.user = userId;
    }
    
    if (status) {
      query.status = status;
    }
    
    const orders = await Order.find(query)
      .populate('user', 'name email phone')
      .populate('items.product')
      .populate('vendor')
      .sort({ createdAt: -1 });
    
    return NextResponse.json({
      success: true,
      data: orders,
    });
  } catch (error) {
    return NextResponse.json(
      { success: false, error: error.message },
      { status: 500 }
    );
  }
}

// POST create new order
export async function POST(request) {
  try {
    const session = await getServerSession(authOptions);
    if (!session) {
      return NextResponse.json({ success: false, error: 'Unauthorized' }, { status: 401 });
    }

    await connectDB();
    const body = await request.json();
    
    const orderData = {
      ...body,
      user: session.user.id,
    };

    const order = await Order.create(orderData);
    
    // Send SMS notification
    if (order.deliveryAddress?.phone) {
      const message = `Order Placed! Your order #${order.orderNumber} for ৳${order.finalAmount} has been received. Track at: ${process.env.NEXTAUTH_URL}/orders/${order._id}`;
      await sendSMS(order.deliveryAddress.phone, message);
    }

    // Send Email notification
    if (session.user.email) {
      await sendEmail({
        to: session.user.email,
        subject: `Order Confirmation - #${order.orderNumber}`,
        html: `<h1>Thank you for your order!</h1><p>Your order for ${order.items.length} items has been received.</p><p>Total: ৳${order.finalAmount}</p>`
      });
    }
    
    return NextResponse.json({
      success: true,
      data: order,
    }, { status: 201 });
  } catch (error) {
    return NextResponse.json(
      { success: false, error: error.message },
      { status: 400 }
    );
  }
}