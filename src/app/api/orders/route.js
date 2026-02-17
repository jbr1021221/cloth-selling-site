import { NextResponse } from 'next/server';
import connectDB from '@/lib/mongodb';
import Order from '@/models/Order';

// GET all orders
export async function GET(request) {
  try {
    await connectDB();
    
    const { searchParams } = new URL(request.url);
    const userId = searchParams.get('userId');
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
    await connectDB();
    const body = await request.json();
    
    const order = await Order.create(body);
    
    // TODO: Send SMS notification
    
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