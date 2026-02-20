<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'admin',
        ]);
        
        // Create Vendor
        $vendor = \App\Models\Vendor::create([
            'name' => 'Fashion Hub',
            'contact_person' => 'John Doe',
            'phone' => '01700000000',
            'email' => 'vendor@fashionhub.com',
            'address' => 'Dhaka, Bangladesh',
            'categories' => ['Shirt', 'T-Shirt', 'Pant'],
            'commission_rate' => 10,
            'is_active' => true,
        ]);

        // Create Sample Products - 50 of each
        $baseProducts = [
            [
                'name' => 'Premium Cotton Shirt',
                'description' => 'High quality cotton shirt for formal wear.',
                'category' => 'Shirt',
                'price' => 1500,
                'discount_price' => 1200,
                'stock' => 50,
                'images' => ['https://images.unsplash.com/photo-1596755094514-f87e34085b2c?q=80&w=500&auto=format&fit=crop'],
                'sizes' => ['M', 'L', 'XL'],
                'colors' => ['White', 'Blue'],
            ],
            [
                'name' => 'Casual T-Shirt',
                'description' => 'Comfortable cotton t-shirt for daily use.',
                'category' => 'T-Shirt',
                'price' => 800,
                'discount_price' => 600,
                'stock' => 100,
                'images' => ['https://images.unsplash.com/photo-1521572267360-ee0c2909d518?q=80&w=500&auto=format&fit=crop'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Black', 'Navy'],
            ],
            [
                'name' => 'Denim Jeans',
                'description' => 'Stylish denim jeans with perfect fit.',
                'category' => 'Jeans',
                'price' => 2500,
                'discount_price' => 2000,
                'stock' => 30,
                'images' => ['https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=500&auto=format&fit=crop'],
                'sizes' => ['30', '32', '34', '36'],
                'colors' => ['Blue', 'Black'],
            ],
            [
                'name' => 'Traditional Saree',
                'description' => 'Beautiful traditional saree for special occasions.',
                'category' => 'Saree',
                'price' => 5000,
                'discount_price' => 4500,
                'stock' => 20,
                'images' => ['https://images.unsplash.com/photo-1610030464440-a88b4202c282?q=80&w=500&auto=format&fit=crop'],
                'colors' => ['Red', 'Gold'],
            ]
        ];

        for ($i = 1; $i <= 50; $i++) {
            foreach ($baseProducts as $productData) {
                // Create a copy so we don't modify the base array
                $newProduct = $productData;
                $newProduct['name'] = $productData['name'] . ' ' . $i; // Make name unique
                $newProduct['sku'] = strtoupper(substr($productData['category'], 0, 3)) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
                $newProduct['vendor_id'] = $vendor->id;
                $newProduct['is_active'] = true;
                
                \App\Models\Product::create($newProduct);
            }
        }
    }
}
