import mongoose from 'mongoose';
import bcrypt from 'bcryptjs';

await mongoose.connect('mongodb://localhost:27017/cloth-selling-site');

const userSchema = new mongoose.Schema({
  name: String,
  email: String,
  password: String,
  role: String,
});

const User = mongoose.models.User || mongoose.model('User', userSchema);

const hashedPassword = await bcrypt.hash('admin123', 10);

await User.deleteOne({ email: 'admin@admin.com' });
await User.create({
  name: 'Admin',
  email: 'admin@admin.com',
  password: hashedPassword,
  role: 'admin',
});

console.log('Admin user created!');
console.log('Email: admin@admin.com');
console.log('Password: admin123');

await mongoose.disconnect();
