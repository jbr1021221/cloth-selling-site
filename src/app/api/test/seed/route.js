import { NextResponse } from 'next/server';
import connectDB from '@/lib/mongodb';
import Product from '@/models/Product';
import User from '@/models/User';
import Order from '@/models/Order';
import Vendor from '@/models/Vendor';
import bcrypt from 'bcryptjs';

export async function GET() {
  try {
    await connectDB();

    // 1. Clear existing data (Optional - use with caution)
    // await User.deleteMany({});
    // await Product.deleteMany({});
    // await Order.deleteMany({});
    // await Vendor.deleteMany({});

    // 2. Create Admin User
    const hashedPassword = await bcrypt.hash('admin123', 10);
    const adminUser = await User.findOneAndUpdate(
      { email: 'admin@clothstore.com' },
      {
        name: 'Admin User',
        password: hashedPassword,
        role: 'admin',
        phone: '01700000000'
      },
      { upsert: true, new: true }
    );

    // 3. Create Sample Vendors
    const vendor1 = await Vendor.findOneAndUpdate(
      { name: 'Elite Fabrics' },
      {
        contactPerson: 'Mr. Rahim',
        phone: '01811111111',
        email: 'rahim@elite.com',
        address: 'Islampur, Dhaka',
        commissionRate: 10
      },
      { upsert: true, new: true }
    );

    // 4. Create Sample Products
    const products = [
      {
        name: 'Premium Cotton Blue Shirt',
        description: 'High-quality cotton shirt perfect for formal and casual wear.',
        price: 1200,
        discountPrice: 990,
        category: 'Shirt',
        stock: 50,
        sizes: ['M', 'L', 'XL'],
        colors: ['Blue', 'White'],
        images: ['https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=1000&auto=format&fit=crop'],
        vendor: vendor1._id
      },
      {
        name: 'Classic Black Slim Fit Jeans',
        description: 'Stretchable and comfortable denim for daily use.',
        price: 1800,
        discountPrice: 1500,
        category: 'Jeans',
        stock: 30,
        sizes: ['30', '32', '34'],
        colors: ['Black'],
        images: ['https://images.unsplash.com/photo-1541099649105-f69ad21f3246?q=80&w=1000&auto=format&fit=crop'],
        vendor: vendor1._id
      },
      {
        name: 'Traditional Silk Saree - Red',
        description: 'Elegant silk saree with beautiful hand-stitched borders.',
        price: 5500,
        discountPrice: 4200,
        category: 'Saree',
        stock: 15,
        sizes: ['Free Size'],
        colors: ['Red', 'Gold'],
        images: ['https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=1000&auto=format&fit=crop'],
        vendor: vendor1._id
      },
      {
        name: 'Cotton Polo T-Shirt',
        description: 'Breathable cotton polo t-shirt for all-day comfort.',
        price: 850,
        discountPrice: 650,
        category: 'T-Shirt',
        stock: 100,
        sizes: ['S', 'M', 'L', 'XL'],
        colors: ['Navy Blue', 'Maroon', 'Black'],
        images: ['https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=1000&auto=format&fit=crop'],
        vendor: vendor1._id
      },
      {
        name: 'Embroidered Kurti - Yellow',
        description: 'Beautifully embroidered kurti for traditional occasions.',
        price: 2200,
        discountPrice: 1850,
        category: 'Kurti',
        stock: 25,
        sizes: ['38', '40', '42'],
        colors: ['Yellow', 'Peach'],
        images: ['https://images.unsplash.com/photo-1583391733956-6c78276477e2?q=80&w=1000&auto=format&fit=crop'],
        vendor: vendor1._id
      },
      {
        name: 'Chino Pants - Beige',
        description: 'Smart casual chino pants for a sharp look.',
        price: 1500,
        discountPrice: 1250,
        category: 'Pant',
        stock: 40,
        sizes: ['30', '32', '34', '36'],
        colors: ['Beige', 'Grey'],
        images: ['https://images.unsplash.com/photo-1473966968600-fa801b869a1a?q=80&w=1000&auto=format&fit=crop'],
        vendor: vendor1._id
      },
      {
        name: 'Floral Print Summer Dress',
        description: 'Lightweight and airy floral dress for summer outings.',
        price: 2500,
        discountPrice: 2100,
        category: 'Others',
        stock: 20,
        sizes: ['S', 'M', 'L'],
        colors: ['White', 'Pink'],
        images: ['https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?q=80&w=1000&auto=format&fit=crop'],
        vendor: vendor1._id
      },
      {
        name: 'Men\'s Formal Leather Shoes',
        description: 'Genuine leather formal shoes for a professional look.',
        price: 3500,
        discountPrice: 2950,
        category: 'Others',
        stock: 15,
        sizes: ['40', '41', '42', '43'],
        colors: ['Brown', 'Black'],
        images: ['https://images.unsplash.com/photo-1533867617858-e7b97e060509?q=80&w=1000&auto=format&fit=crop'],
        vendor: vendor1._id
      },
      {
        name: 'Denim Jacket - Classic Blue',
        description: 'Rugged denim jacket that never goes out of style.',
        price: 2800,
        discountPrice: 2400,
        category: 'Others',
        stock: 25,
        sizes: ['M', 'L', 'XL'],
        colors: ['Blue'],
        images: ['https://images.unsplash.com/photo-1523205771623-e0faa4d2813d?q=80&w=1000&auto=format&fit=crop'],
        vendor: vendor1._id
      },
      {
        name: 'Linen Casual Trousers',
        description: 'Comfortable linen trousers for a relaxed tropical vibe.',
        price: 1600,
        discountPrice: 1400,
        category: 'Pant',
        stock: 35,
        sizes: ['30', '32', '34'],
        colors: ['Off-White', 'Olive'],
        images: ['https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?q=80&w=1000&auto=format&fit=crop'],
        vendor: vendor1._id
      }
    ];

    for (const p of products) {
      await Product.findOneAndUpdate({ name: p.name }, p, { upsert: true });
    }

    // 5. Create Sample Orders
    const sampleProducts = await Product.find().limit(2);
    const orderData = {
      user: adminUser._id,
      items: sampleProducts.map(p => ({
        product: p._id,
        name: p.name,
        price: p.discountPrice || p.price,
        quantity: 1,
        size: p.sizes[0],
        color: p.colors[0],
        image: p.images[0]
      })),
      totalAmount: 2490,
      shippingCharge: 60,
      finalAmount: 2550,
      paymentMethod: 'COD',
      paymentStatus: 'Pending',
      status: 'Pending',
      deliveryAddress: {
        name: 'John Doe',
        phone: '01912345678',
        address: 'House 12, Road 5, Dhanmondi',
        city: 'Dhaka',
        postalCode: '1205'
      }
    };

    await Order.create(orderData);

    return NextResponse.json({
      success: true,
      message: 'Dummy data seeded successfully!',
      credentials: {
        email: 'admin@clothstore.com',
        password: 'admin123'
      }
    });
  } catch (error) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
