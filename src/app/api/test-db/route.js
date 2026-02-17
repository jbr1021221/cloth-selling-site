import connectDB from '@/lib/mongodb';
import { NextResponse } from 'next/server';

export async function GET() {
  try {
    const db = await connectDB();
    const state = db.connection.readyState;
    // 0: disconnected, 1: connected, 2: connecting, 3: disconnecting
    if (state === 1) {
      return NextResponse.json({ 
        msg: 'Database connected successfully',
        dbName: db.connection.name
      }, { status: 200 });
    } else {
      return NextResponse.json({ 
        msg: 'Database connection failed', 
        state 
      }, { status: 500 });
    }
  } catch (error) {
    return NextResponse.json({ msg: 'Database connection error', error: error.message }, { status: 500 });
  }
}
