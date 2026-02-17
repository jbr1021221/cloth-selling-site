import mongoose from 'mongoose';

const UserSchema = new mongoose.Schema({
  name: {
    type: String,
    required: [true, 'Please provide a name'],
    maxlength: [60, 'Name cannot be more than 60 characters'],
  },
  email: {
    type: String,
    required: [true, 'Please provide an email'],
    unique: true,
    match: [
      /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,
      'Please add a valid email',
    ],
  },
  password: {
    type: String,
    required: [true, 'Please provide a password'],
    minlength: [6, 'Password must be at least 6 characters'],
    select: false, // Don't return password by default
  },
  role: {
    type: String,
    enum: ['customer', 'admin', 'vendor'],
    default: 'customer',
  },
  phone: {
    type: String,
    unique: true,
    sparse: true, // Allow multiple null/undefined values
  },
  address: {
    street: String,
    city: String,
    state: String,
    zip: String,
    country: String,
  },
}, { timestamps: true });

export default mongoose.models.User || mongoose.model('User', UserSchema);