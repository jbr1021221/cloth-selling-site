import mongoose from 'mongoose';

const SMSCampaignSchema = new mongoose.Schema({
  message: {
    type: String,
    required: true,
    maxlength: 160,
  },
  recipients: [{
    phone: String,
    name: String,
  }],
  recipientCount: {
    type: Number,
    required: true,
  },
  status: {
    type: String,
    enum: ['Draft', 'Sent', 'Failed'],
    default: 'Draft',
  },
  sentAt: {
    type: Date,
  },
  createdBy: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
  },
  createdAt: {
    type: Date,
    default: Date.now,
  },
});

export default mongoose.models.SMSCampaign || mongoose.model('SMSCampaign', SMSCampaignSchema);