import { NextResponse } from 'next/server';
import connectDB from '@/lib/mongodb';
import User from '@/models/User';

export async function GET(request) {
  try {
    await connectDB();
    const { searchParams } = new URL(request.url);
    const role = searchParams.get('role');
    
    let query = {};
    if (role) {
      query.role = role;
    }
    
    const users = await User.find(query).select('-password').sort({ createdAt: -1 });
    
    return NextResponse.json({
      success: true,
      data: users,
    });
  } catch (error) {
    return NextResponse.json(
      { success: false, error: error.message },
      { status: 500 }
    );
  }
}
