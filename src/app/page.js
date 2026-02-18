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
      <section className="bg-white py-24">
        <div className="container mx-auto px-4 text-center">
          <div className="mb-16">
            <h2 className="text-4xl md:text-5xl font-bold mb-4 text-gray-900">
              Shop by Category
            </h2>
            <p className="text-gray-500 text-lg max-w-2xl mx-auto">
              Find exactly what you're looking for across our diverse collections
            </p>
          </div>
          
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            {[
              { 
                name: 'Shirt', 
                link: '/products?category=Shirt',
                image: 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?q=80&w=500&auto=format&fit=crop'
              },
              { 
                name: 'T-Shirt', 
                link: '/products?category=T-Shirt',
                image: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?q=80&w=500&auto=format&fit=crop'
              },
              { 
                name: 'Pant', 
                link: '/products?category=Pant',
                image: 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?q=80&w=500&auto=format&fit=crop'
              },
              { 
                name: 'Saree', 
                link: '/products?category=Saree',
                image: 'https://images.unsplash.com/photo-1610030464440-a88b4202c282?q=80&w=500&auto=format&fit=crop'
              },
              { 
                name: 'Jeans', 
                link: '/products?category=Jeans',
                image: 'https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=500&auto=format&fit=crop'
              },
              { 
                name: 'Kurti', 
                link: '/products?category=Kurti',
                image: 'https://images.unsplash.com/photo-1620601815142-b83d8ca237bd?q=80&w=500&auto=format&fit=crop'
              },
              { 
                name: 'Salwar', 
                link: '/products?category=Salwar',
                image: 'https://images.unsplash.com/photo-1617173944883-6ffbd35d584d?q=80&w=500&auto=format&fit=crop'
              },
              { 
                name: 'Others', 
                link: '/products?category=Others',
                image: 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?q=80&w=500&auto=format&fit=crop'
              },
            ].map((category) => (
              <Link
                key={category.name}
                href={category.link}
                className="group bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
              >
                <div className="relative h-64 w-full overflow-hidden">
                  <Image 
                    src={category.image}
                    alt={category.name}
                    fill
                    className="object-cover transform group-hover:scale-110 transition-transform duration-500"
                  />
                  <div className="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </div>
                <div className="p-6">
                  <h3 className="text-xl font-bold text-gray-800 mb-1">
                    {category.name}
                  </h3>
                  <span className="text-sm text-blue-500 font-medium group-hover:translate-x-1 inline-block transition-transform">
                    Explore Collection →
                  </span>
                </div>
              </Link>
            ))}
          </div>
        </div>
        
        {/* Purple/Pink Gradient Bar */}
        <div className="w-full h-24 bg-gradient-to-r from-purple-600 via-fuchsia-500 to-pink-500 mt-12 opacity-90"></div>
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