import { NextResponse } from 'next/server';
import connectDB from '@/lib/mongodb';
import SMSCampaign from '@/models/SMSCampaign';

export async function GET() {
  try {
    await connectDB();
    const campaigns = await SMSCampaign.find({}).sort({ createdAt: -1 });
    return NextResponse.json({
      success: true,
      data: campaigns,
    });
  } catch (error) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

export async function POST(request) {
  try {
    await connectDB();
    const body = await request.json();
    
    // In a real application, you would integrate with an SMS gateway here
    // Example: sendSMS(body.recipients, body.message)
    
    const campaign = await SMSCampaign.create({
      ...body,
      status: 'Sent', // Mark as sent for this demo
      sentAt: new Date(),
    });
    
    return NextResponse.json({
      success: true,
      data: campaign,
    });
  } catch (error) {
    return NextResponse.json({ success: false, error: error.message }, { status: 400 });
  }
}
