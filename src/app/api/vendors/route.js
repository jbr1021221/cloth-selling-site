import { NextResponse } from 'next/server';
import connectDB from '@/lib/mongodb';
import Vendor from '@/models/Vendor';

export async function GET() {
  try {
    await connectDB();
    const vendors = await Vendor.find({}).sort({ createdAt: -1 });
    return NextResponse.json({
      success: true,
      data: vendors,
    });
  } catch (error) {
    return NextResponse.json(
      { success: false, error: error.message },
      { status: 500 }
    );
  }
}

export async function POST(request) {
  try {
    await connectDB();
    const body = await request.json();
    
    const vendor = await Vendor.create(body);
    
    return NextResponse.json({
      success: true,
      data: vendor,
    });
  } catch (error) {
    return NextResponse.json(
      { success: false, error: error.message },
      { status: 400 }
    );
  }
}
