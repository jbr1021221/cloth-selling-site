'use client';

import Image from 'next/image';
import Link from 'next/link';
import useCartStore from '@/store/cartStore';
import toast from 'react-hot-toast';

export default function ProductCard({ product }) {
  const addItem = useCartStore((state) => state.addItem);

  const handleAddToCart = () => {
    addItem(product, 1, product.sizes[0], product.colors[0]);
    toast.success('Added to cart!');
  };

  const displayPrice = product.discountPrice || product.price;
  const hasDiscount = product.discountPrice && product.discountPrice < product.price;

  return (
    <div className="border rounded-lg overflow-hidden shadow-md hover:shadow-xl transition group">
      <Link href={`/products/${product._id}`}>
        <div className="relative h-64 overflow-hidden">
          <Image
            src={product.images[0] || '/placeholder.jpg'}
            alt={product.name}
            fill
            className="object-cover group-hover:scale-110 transition duration-300"
          />
          {hasDiscount && (
            <div className="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-semibold">
              {Math.round(((product.price - product.discountPrice) / product.price) * 100)}% OFF
            </div>
          )}
        </div>
      </Link>

      <div className="p-4">
        <Link href={`/products/${product._id}`}>
          <h3 className="text-lg font-semibold mb-2 hover:text-blue-600 line-clamp-2">
            {product.name}
          </h3>
        </Link>
        
        <p className="text-sm text-gray-500 mb-2">{product.category}</p>
        
        <div className="flex items-center gap-2 mb-4">
          <span className="text-xl font-bold text-blue-600">
            ৳{displayPrice}
          </span>
          {hasDiscount && (
            <span className="text-sm text-gray-400 line-through">
              ৳{product.price}
            </span>
          )}
        </div>

        <button
          onClick={handleAddToCart}
          className="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition"
        >
          Add to Cart
        </button>
      </div>
    </div>
  );
}