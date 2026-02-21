<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Support\Str;

class ClothingProductSeeder extends Seeder
{
    public function run(): void
    {
        // STEP 1: Clear old data safely
        // Disable foreign key checks to allow truncating tables with foreign key constraints
        DB::table('order_items')->delete();
        DB::table('order_status_histories')->delete();
        DB::table('orders')->delete();
        DB::table('reviews')->delete();
        DB::table('wishlists')->delete();
        DB::table('loyalty_points')->delete();
        DB::table('flash_sales')->delete();
        DB::table('product_variants')->delete();
        DB::table('products')->delete();

        // Ensure we have a vendor
        $vendor = Vendor::first();
        if (!$vendor) {
            $vendor = Vendor::create([
                'name' => 'Main Vendor',
                'contact_person' => 'Admin',
                'email' => 'vendor@clothstore.com',
                'phone' => '01711223344',
                'address' => 'Dhaka, Bangladesh',
                'is_active' => true,
            ]);
        }

        // STEP 2: New Product Seeder Data
        $products = [
            // --- SHIRTS (8 products) ---
            [
                'name' => 'Classic Oxford Shirt',
                'category' => 'Shirt',
                'price' => 850,
                'discounted_price' => 650,
                'stock' => 45,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Blue', 'Gray'],
                'images' => ['oxford1', 'oxford2'],
                'desc' => 'A timeless classic oxford shirt made from premium breathable cotton. Perfect for both office wear and casual outings.'
            ],
            [
                'name' => 'Casual Linen Shirt',
                'category' => 'Shirt',
                'price' => 750,
                'discounted_price' => 580,
                'stock' => 38,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['White', 'Beige', 'Light Blue'],
                'images' => ['linen1', 'linen2'],
                'desc' => 'Stay cool and comfortable with our lightweight linen shirt. Offers a relaxed fit for tropical weather.'
            ],
            [
                'name' => 'Formal Business Shirt',
                'category' => 'Shirt',
                'price' => 1200,
                'discounted_price' => 950,
                'stock' => 30,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Light Blue', 'Pink'],
                'images' => ['formal1', 'formal2'],
                'desc' => 'Elevate your professional look with this crisp, formal business shirt. Features a structured collar and wrinkle-resistant fabric.'
            ],
            [
                'name' => 'Slim Fit Check Shirt',
                'category' => 'Shirt',
                'price' => 950,
                'discounted_price' => 750,
                'stock' => 25,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Blue', 'Green', 'Red'],
                'images' => ['check1', 'check2'],
                'desc' => 'Modern slim fit shirt with a stylish check pattern. Great for pairing with jeans or chinos.'
            ],
            [
                'name' => 'Premium Cotton Shirt',
                'category' => 'Shirt',
                'price' => 1100,
                'discounted_price' => 880,
                'stock' => 20,
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Navy', 'Gray'],
                'images' => ['cotton1', 'cotton2'],
                'desc' => 'Crafted from 100% premium staple cotton for ultimate softness. Ensures all-day comfort and durability.'
            ],
            [
                'name' => 'Printed Casual Shirt',
                'category' => 'Shirt',
                'price' => 680,
                'discounted_price' => 520,
                'stock' => 50,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Multicolor'],
                'images' => ['printed1', 'printed2'],
                'desc' => 'Make a statement with this vibrant printed casual shirt. Designed for a fun, relaxed weekend vibe.'
            ],
            [
                'name' => 'Half Sleeve Shirt',
                'category' => 'Shirt',
                'price' => 620,
                'discounted_price' => 480,
                'stock' => 60,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Blue', 'Gray', 'Black'],
                'images' => ['halfslv1', 'halfslv2'],
                'desc' => 'A wardrobe essential for summer. This half-sleeve shirt offers maximum airflow and style.'
            ],
            [
                'name' => 'Denim Shirt',
                'category' => 'Shirt',
                'price' => 1350,
                'discounted_price' => 1100,
                'stock' => 18,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Light Blue', 'Dark Blue'],
                'images' => ['denim1', 'denim2'],
                'desc' => 'Rugged and stylish denim shirt that only gets better with age. Features durable stitching and snap buttons.'
            ],

            // --- T-SHIRTS (8 products) ---
            [
                'name' => 'Basic Round Neck T-Shirt',
                'category' => 'T-Shirt',
                'price' => 450,
                'discounted_price' => 350,
                'stock' => 80,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Black', 'Navy', 'Red', 'Gray'],
                'images' => ['tshirt1', 'tshirt2'],
                'desc' => 'The ultimate everyday basic round neck t-shirt. Made with a soft cotton blend for a perfect regular fit.'
            ],
            [
                'name' => 'Graphic Print T-Shirt',
                'category' => 'T-Shirt',
                'price' => 550,
                'discounted_price' => 420,
                'stock' => 65,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['White', 'Black', 'Gray'],
                'images' => ['graphic1', 'graphic2'],
                'desc' => 'Show off your personality with our unique graphic print tees. High-quality prints that won\'t fade.'
            ],
            [
                'name' => 'Premium Polo T-Shirt',
                'category' => 'T-Shirt',
                'price' => 750,
                'discounted_price' => 600,
                'stock' => 45,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Navy', 'Black', 'Red'],
                'images' => ['polo1', 'polo2'],
                'desc' => 'Smart-casual polo shirt with a classic collar and placket. Ideal for golf, office casuals, or evening outs.'
            ],
            [
                'name' => 'V-Neck T-Shirt',
                'category' => 'T-Shirt',
                'price' => 420,
                'discounted_price' => 320,
                'stock' => 70,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['White', 'Gray', 'Black', 'Navy'],
                'images' => ['vneck1', 'vneck2'],
                'desc' => 'Flattering V-neck design that adds a touch of style to a simple tee. Soft, breathable, and versatile.'
            ],
            [
                'name' => 'Oversized T-Shirt',
                'category' => 'T-Shirt',
                'price' => 580,
                'discounted_price' => 450,
                'stock' => 55,
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Black', 'Beige', 'Gray'],
                'images' => ['oversize1', 'oversize2'],
                'desc' => 'On-trend oversized fit for ultimate street style and comfort. Features dropped shoulders and a heavy-weight drape.'
            ],
            [
                'name' => 'Striped T-Shirt',
                'category' => 'T-Shirt',
                'price' => 480,
                'discounted_price' => 380,
                'stock' => 40,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Navy/White', 'Black/White', 'Red/White'],
                'images' => ['stripe1', 'stripe2'],
                'desc' => 'Classic Breton stripes that never go out of fashion. A nautical-inspired look for casual days.'
            ],
            [
                'name' => 'Sports Dry-Fit T-Shirt',
                'category' => 'T-Shirt',
                'price' => 520,
                'discounted_price' => 400,
                'stock' => 75,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['Black', 'Navy', 'Red', 'Green'],
                'images' => ['sports1', 'sports2'],
                'desc' => 'High-performance dry-fit tee designed for active lifestyles. Wicks sweat away rapidly during workouts.'
            ],
            [
                'name' => 'Henley Neck T-Shirt',
                'category' => 'T-Shirt',
                'price' => 490,
                'discounted_price' => 380,
                'stock' => 35,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['White', 'Gray', 'Navy', 'Olive'],
                'images' => ['henley1', 'henley2'],
                'desc' => 'Distinctive buttoned Henley neckline for a rugged, stylish look. A great alternative to basic crew necks.'
            ],

            // --- JEANS (6 products) ---
            [
                'name' => 'Slim Fit Blue Jeans',
                'category' => 'Jeans',
                'price' => 1800,
                'discounted_price' => 1450,
                'stock' => 30,
                'sizes' => ['28', '30', '32', '34', '36'],
                'colors' => ['Light Blue', 'Dark Blue'],
                'images' => ['jeans1', 'jeans2'],
                'desc' => 'Classic slim fit jeans with a slight stretch for comfort. The perfect tailored silhouette for everyday wear.'
            ],
            [
                'name' => 'Regular Fit Jeans',
                'category' => 'Jeans',
                'price' => 1600,
                'discounted_price' => 1280,
                'stock' => 25,
                'sizes' => ['28', '30', '32', '34', '36', '38'],
                'colors' => ['Dark Blue', 'Black', 'Gray'],
                'images' => ['jeans3', 'jeans4'],
                'desc' => 'Traditional regular fit offering straight lines from hip to hem. Unbeatable comfort with classic denim styling.'
            ],
            [
                'name' => 'Skinny Fit Jeans',
                'category' => 'Jeans',
                'price' => 1750,
                'discounted_price' => 1400,
                'stock' => 20,
                'sizes' => ['28', '30', '32', '34'],
                'colors' => ['Light Blue', 'Black'],
                'images' => ['jeans5', 'jeans6'],
                'desc' => 'Ultra-modern skinny fit that hugs the legs. Made with high-stretch denim for unrestricted movement.'
            ],
            [
                'name' => 'Cargo Jeans',
                'category' => 'Jeans',
                'price' => 1950,
                'discounted_price' => 1600,
                'stock' => 15,
                'sizes' => ['30', '32', '34', '36'],
                'colors' => ['Dark Blue', 'Olive', 'Black'],
                'images' => ['cargo1', 'cargo2'],
                'desc' => 'Utility-inspired cargo jeans with convenient side pockets. Blends rugged functionality with urban street style.'
            ],
            [
                'name' => 'Straight Cut Jeans',
                'category' => 'Jeans',
                'price' => 1700,
                'discounted_price' => 1350,
                'stock' => 28,
                'sizes' => ['28', '30', '32', '34', '36'],
                'colors' => ['Medium Blue', 'Dark Blue', 'Black'],
                'images' => ['straight1', 'straight2'],
                'desc' => 'A timeless straight cut that fits perfectly over boots or sneakers. An essential foundation for any wardrobe.'
            ],
            [
                'name' => 'Ripped Jeans',
                'category' => 'Jeans',
                'price' => 2100,
                'discounted_price' => 1700,
                'stock' => 12,
                'sizes' => ['28', '30', '32', '34'],
                'colors' => ['Light Blue', 'Medium Blue'],
                'images' => ['ripped1', 'ripped2'],
                'desc' => 'Edgy distressed detailing and rips for a lived-in vintage look. Expertly crafted for authentic style.'
            ],

            // --- PANJABI (6 products) ---
            [
                'name' => 'Classic White Panjabi',
                'category' => 'Panjabi',
                'price' => 1200,
                'discounted_price' => 950,
                'stock' => 35,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White'],
                'images' => ['panjabi1', 'panjabi2'],
                'desc' => 'The essential pure white Panjabi. Simple, elegant, and perfect for Jumma or casual traditional gatherings.'
            ],
            [
                'name' => 'Embroidered Panjabi',
                'category' => 'Panjabi',
                'price' => 2500,
                'discounted_price' => 2000,
                'stock' => 20,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Cream', 'Light Yellow'],
                'images' => ['panjabi3', 'panjabi4'],
                'desc' => 'Premium Panjabi featuring intricate thread embroidery on the collar and placket. A sophisticated choice for special events.'
            ],
            [
                'name' => 'Cotton Panjabi',
                'category' => 'Panjabi',
                'price' => 950,
                'discounted_price' => 750,
                'stock' => 40,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Light Blue', 'Cream'],
                'images' => ['panjabi5', 'panjabi6'],
                'desc' => 'Breathable, lightweight cotton Panjabi tailored for the hot Bangladeshi climate. Ideal for daily or casual wear.'
            ],
            [
                'name' => 'Eid Special Panjabi',
                'category' => 'Panjabi',
                'price' => 3500,
                'discounted_price' => 2800,
                'stock' => 15,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Cream', 'Gold'],
                'images' => ['eidpanjabi1', 'eidpanjabi2'],
                'desc' => 'Our exclusive Eid collection Panjabi. Features luxurious fabric blends and stunning embellishments to make you stand out.'
            ],
            [
                'name' => 'Printed Panjabi',
                'category' => 'Panjabi',
                'price' => 1100,
                'discounted_price' => 880,
                'stock' => 25,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Green', 'Blue', 'Maroon'],
                'images' => ['printpanjabi1', 'printpanjabi2'],
                'desc' => 'Vibrant printed motives on soft fabric. Adds a contemporary and artistic twist to traditional Panjabis.'
            ],
            [
                'name' => 'Linen Panjabi',
                'category' => 'Panjabi',
                'price' => 1800,
                'discounted_price' => 1450,
                'stock' => 18,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['White', 'Beige', 'Light Gray'],
                'images' => ['linenpanj1', 'linenpanj2'],
                'desc' => 'Fluid, richly textured linen Panjabi that drapes beautifully. Offers a regal, understated elegance.'
            ],

            // --- SAREE (6 products) ---
            [
                'name' => 'Cotton Saree',
                'category' => 'Saree',
                'price' => 1500,
                'discounted_price' => 1200,
                'stock' => 25,
                'sizes' => ['Free Size'],
                'colors' => ['Red', 'Blue', 'Green', 'Yellow'],
                'images' => ['saree1', 'saree2'],
                'desc' => 'Comfortable, pure cotton daily-wear saree. Features classic borders and a soft texture that\'s easy to drape.'
            ],
            [
                'name' => 'Silk Saree',
                'category' => 'Saree',
                'price' => 4500,
                'discounted_price' => 3800,
                'stock' => 10,
                'sizes' => ['Free Size'],
                'colors' => ['Red', 'Maroon', 'Gold', 'Green'],
                'images' => ['saree3', 'saree4'],
                'desc' => 'Exquisite silk saree with a lustrous sheen and rich zari work. The perfect traditional attire for weddings and grand occasions.'
            ],
            [
                'name' => 'Jamdani Saree',
                'category' => 'Saree',
                'price' => 8000,
                'discounted_price' => 6500,
                'stock' => 8,
                'sizes' => ['Free Size'],
                'colors' => ['White/Red', 'White/Green', 'White/Blue'],
                'images' => ['jamdani1', 'jamdani2'],
                'desc' => 'Authentic handloom Jamdani saree, a masterpiece of Bangladeshi heritage. Features delicate, transparent woven motifs.'
            ],
            [
                'name' => 'Georgette Saree',
                'category' => 'Saree',
                'price' => 2800,
                'discounted_price' => 2200,
                'stock' => 15,
                'sizes' => ['Free Size'],
                'colors' => ['Pink', 'Purple', 'Blue', 'Red'],
                'images' => ['georgette1', 'georgette2'],
                'desc' => 'Lightweight, flowing Georgette saree that flatters any silhouette. Easy to manage and features elegant modern prints.'
            ],
            [
                'name' => 'Printed Saree',
                'category' => 'Saree',
                'price' => 1800,
                'discounted_price' => 1450,
                'stock' => 20,
                'sizes' => ['Free Size'],
                'colors' => ['Multicolor'],
                'images' => ['printsaree1', 'printsaree2'],
                'desc' => 'Charming printed saree with vivid floral and geometric patterns. Great for daytime events or relaxed get-togethers.'
            ],
            [
                'name' => 'Party Wear Saree',
                'category' => 'Saree',
                'price' => 5500,
                'discounted_price' => 4500,
                'stock' => 12,
                'sizes' => ['Free Size'],
                'colors' => ['Royal Blue', 'Maroon', 'Emerald Green'],
                'images' => ['partysaree1', 'partysaree2'],
                'desc' => 'Glamorous party-ready saree adorned with sequins and stonework. Designed to make you the center of attention.'
            ],

            // --- KIDS (6 products) ---
            [
                'name' => 'Kids Cotton T-Shirt',
                'category' => 'Kids',
                'price' => 320,
                'discounted_price' => 250,
                'stock' => 60,
                'sizes' => ['2Y', '4Y', '6Y', '8Y', '10Y', '12Y'],
                'colors' => ['Red', 'Blue', 'Yellow', 'Green'],
                'images' => ['kids1', 'kids2'],
                'desc' => 'Playful and bright cotton t-shirts for kids. Specially woven to be gentle on sensitive skin during active play.'
            ],
            [
                'name' => 'Kids Jeans',
                'category' => 'Kids',
                'price' => 850,
                'discounted_price' => 680,
                'stock' => 35,
                'sizes' => ['4Y', '6Y', '8Y', '10Y', '12Y'],
                'colors' => ['Blue', 'Dark Blue'],
                'images' => ['kids3', 'kids4'],
                'desc' => 'Durable kids\' jeans built to withstand rough and tumble. Features an adjustable inner waistband for growing children.'
            ],
            [
                'name' => 'Kids Panjabi Set',
                'category' => 'Kids',
                'price' => 1200,
                'discounted_price' => 950,
                'stock' => 25,
                'sizes' => ['2Y', '4Y', '6Y', '8Y', '10Y'],
                'colors' => ['White', 'Cream', 'Light Blue'],
                'images' => ['kids5', 'kids6'],
                'desc' => 'Adorable miniature Panjabi matching sets for boys. Complete with comfortable pyjamas for festive occasions.'
            ],
            [
                'name' => 'Baby Frock',
                'category' => 'Kids',
                'price' => 680,
                'discounted_price' => 520,
                'stock' => 30,
                'sizes' => ['1Y', '2Y', '3Y', '4Y'],
                'colors' => ['Pink', 'Yellow', 'White', 'Purple'],
                'images' => ['kids7', 'kids8'],
                'desc' => 'Cute, twirl-worthy frocks for little girls. Features delicate bows and soft, harmless stitching.'
            ],
            [
                'name' => 'Kids Shirt',
                'category' => 'Kids',
                'price' => 480,
                'discounted_price' => 380,
                'stock' => 45,
                'sizes' => ['4Y', '6Y', '8Y', '10Y', '12Y'],
                'colors' => ['White', 'Blue', 'Stripes'],
                'images' => ['kids9', 'kids10'],
                'desc' => 'Smart casual shirts to make the little ones look sharp. Easy to wash and iron.'
            ],
            [
                'name' => 'Kids Tracksuit',
                'category' => 'Kids',
                'price' => 950,
                'discounted_price' => 750,
                'stock' => 20,
                'sizes' => ['4Y', '6Y', '8Y', '10Y', '12Y'],
                'colors' => ['Navy', 'Black', 'Red'],
                'images' => ['kids11', 'kids12'],
                'desc' => 'Cozy and athletic tracksuits for colder days or sports practice. Super comfy brushed fleece interior.'
            ],
        ];

        foreach ($products as $item) {
            $formattedImages = collect($item['images'])->map(function($seed) {
                return "https://picsum.photos/seed/{$seed}/800/1000";
            })->toArray();

            Product::create([
                'vendor_id' => $vendor->id,
                'name' => $item['name'],
                'description' => $item['desc'],
                'price' => $item['price'],
                'discount_price' => $item['discounted_price'],
                'stock' => $item['stock'],
                'category' => $item['category'],
                'images' => $formattedImages,
                'is_active' => true,
                'colors' => $item['colors'],
                'sizes' => $item['sizes'],
            ]);
        }
    }
}
