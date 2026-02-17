import Link from 'next/link';
import Image from 'next/image';
import ProductCard from '@/components/products/ProductCard';
import { FiTruck, FiShield, FiCreditCard, FiHeadphones } from 'react-icons/fi';

async function getProducts() {
  try {
    const res = await fetch(`${process.env.NEXTAUTH_URL}/api/products`, {
      cache: 'no-store',
    });
    
    if (!res.ok) return { data: [] };
    
    const result = await res.json();
    return result;
  } catch (error) {
    console.error('Error fetching products:', error);
    return { data: [] };
  }
}

export default async function Home() {
  const { data: products } = await getProducts();
  const featuredProducts = products.slice(0, 8);

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white overflow-hidden">
        <div className="absolute inset-0 bg-black opacity-10"></div>
        <div className="container mx-auto px-4 py-24 md:py-32 relative z-10">
          <div className="max-w-3xl mx-auto text-center">
            <h1 className="text-5xl md:text-7xl font-extrabold mb-6 leading-tight">
              Fashion That Speaks
              <span className="block text-yellow-300">Your Style</span>
            </h1>
            <p className="text-xl md:text-2xl mb-10 text-gray-100">
              Discover premium quality clothing at prices you'll love
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link
                href="/products"
                className="bg-white text-purple-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1"
              >
                Shop Now
              </Link>
              <Link
                href="/products?category=Saree"
                className="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-purple-600 transition"
              >
                Explore Collections
              </Link>
            </div>
          </div>
        </div>
        
        {/* Decorative Elements */}
        <div className="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
        <div className="absolute bottom-0 left-0 w-96 h-96 bg-white opacity-5 rounded-full -ml-48 -mb-48"></div>
      </section>

      {/* Features Section */}
      <section className="py-12 bg-white border-b">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div className="text-center">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-blue-600 rounded-full mb-3">
                <FiTruck size={28} />
              </div>
              <h3 className="font-semibold mb-1">Free Shipping</h3>
              <p className="text-sm text-gray-500">On orders over ৳1000</p>
            </div>
            <div className="text-center">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-green-100 text-green-600 rounded-full mb-3">
                <FiShield size={28} />
              </div>
              <h3 className="font-semibold mb-1">Secure Payment</h3>
              <p className="text-sm text-gray-500">100% secure checkout</p>
            </div>
            <div className="text-center">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-purple-100 text-purple-600 rounded-full mb-3">
                <FiCreditCard size={28} />
              </div>
              <h3 className="font-semibold mb-1">Easy Returns</h3>
              <p className="text-sm text-gray-500">7 days return policy</p>
            </div>
            <div className="text-center">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-orange-100 text-orange-600 rounded-full mb-3">
                <FiHeadphones size={28} />
              </div>
              <h3 className="font-semibold mb-1">24/7 Support</h3>
              <p className="text-sm text-gray-500">Dedicated support</p>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Products */}
      <section className="container mx-auto px-4 py-20">
        <div className="text-center mb-12">
          <h2 className="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
            Featured Products
          </h2>
          <p className="text-gray-600 text-lg">
            Handpicked items just for you
          </p>
        </div>
        
        {featuredProducts.length > 0 ? (
          <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
              {featuredProducts.map((product) => (
                <ProductCard key={product._id} product={product} />
              ))}
            </div>
            <div className="text-center">
              <Link
                href="/products"
                className="inline-block bg-gradient-to-r from-purple-600 to-pink-600 text-white px-10 py-4 rounded-full font-bold text-lg hover:shadow-xl transition transform hover:-translate-y-1"
              >
                View All Products →
              </Link>
            </div>
          </>
        ) : (
          <div className="text-center py-16">
            <div className="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-4">
              <svg className="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <h3 className="text-2xl font-bold mb-2">No Products Yet</h3>
            <p className="text-gray-500 mb-6">We're adding amazing products soon!</p>
            <Link
              href="/products"
              className="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700"
            >
              Check Back Later
            </Link>
          </div>
        )}
      </section>

      {/* Categories Section */}
      <section className="bg-gradient-to-br from-gray-50 to-gray-100 py-20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl md:text-5xl font-bold mb-4">
              Shop by Category
            </h2>
            <p className="text-gray-600 text-lg">
              Find exactly what you're looking for
            </p>
          </div>
          
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
            {[
              { name: 'Shirt', emoji: '👔', color: 'from-blue-400 to-blue-600' },
              { name: 'T-Shirt', emoji: '👕', color: 'from-green-400 to-green-600' },
              { name: 'Pant', emoji: '👖', color: 'from-purple-400 to-purple-600' },
              { name: 'Saree', emoji: '🥻', color: 'from-pink-400 to-pink-600' },
            ].map((category) => (
              <Link
                key={category.name}
                href={`/products?category=${category.name}`}
                className="group relative bg-white rounded-2xl p-8 text-center hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
              >
                <div className={`absolute inset-0 bg-gradient-to-br ${category.color} opacity-0 group-hover:opacity-10 transition-opacity`}></div>
                <div className="text-6xl mb-4">{category.emoji}</div>
                <h3 className="text-xl font-bold text-gray-800 group-hover:text-purple-600 transition">
                  {category.name}
                </h3>
                <p className="text-sm text-gray-500 mt-2">Explore Collection →</p>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="bg-gradient-to-r from-purple-600 to-pink-600 text-white py-20">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-4xl md:text-5xl font-bold mb-6">
            Ready to Upgrade Your Wardrobe?
          </h2>
          <p className="text-xl mb-8 text-gray-100">
            Join thousands of satisfied customers shopping with us
          </p>
          <Link
            href="/register"
            className="inline-block bg-white text-purple-600 px-10 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1"
          >
            Create Account
          </Link>
        </div>
      </section>
    </div>
  );
}