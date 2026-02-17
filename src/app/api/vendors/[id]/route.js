import { NextResponse } from 'next/server';
import connectDB from '@/lib/mongodb';
import Vendor from '@/models/Vendor';

export async function GET(request, { params }) {
  try {
    await connectDB();
    const vendor = await Vendor.findById(params.id);
    if (!vendor) {
      return NextResponse.json({ success: false, error: 'Vendor not found' }, { status: 404 });
    }
    return NextResponse.json({ success: true, data: vendor });
  } catch (error) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

export async function PUT(request, { params }) {
  try {
    await connectDB();
    const body = await request.json();
    const vendor = await Vendor.findByIdAndUpdate(params.id, body, { new: true, runValidators: true });
    if (!vendor) {
      return NextResponse.json({ success: false, error: 'Vendor not found' }, { status: 404 });
    }
    return NextResponse.json({ success: true, data: vendor });
  } catch (error) {
    return NextResponse.json({ success: false, error: error.message }, { status: 400 });
  }
}

export async function DELETE(request, { params }) {
  try {
    await connectDB();
    const vendor = await Vendor.findByIdAndDelete(params.id);
    if (!vendor) {
      return NextResponse.json({ success: false, error: 'Vendor not found' }, { status: 404 });
    }
    return NextResponse.json({ success: true, data: vendor });
  } catch (error) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
