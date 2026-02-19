'use client';

import { useState } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { FiShoppingCart, FiHeart, FiShare2 } from 'react-icons/fi';
import useCartStore from '@/store/cartStore';
import ProductCard from './ProductCard';
import toast from 'react-hot-toast';

export default function ProductDetail({ product, relatedProducts }) {
  const [selectedImage, setSelectedImage] = useState(0);
  const [selectedSize, setSelectedSize] = useState(product.sizes?.[0] || '');
  const [selectedColor, setSelectedColor] = useState(product.colors?.[0] || '');
  const [quantity, setQuantity] = useState(1);

  const addItem = useCartStore((state) => state.addItem);

  const handleAddToCart = () => {
    if (!selectedSize || !selectedColor) {
      toast.error('Please select size and color');
      return;
    }

    addItem(product, quantity, selectedSize, selectedColor);
    toast.success('Added to cart!');
  };

  const discountPrice = product.discountPrice || product.discount_price;
  const displayPrice = discountPrice || product.price;
  const hasDiscount = discountPrice && discountPrice < product.price;
  const discountPercent = hasDiscount
    ? Math.round(((product.price - discountPrice) / product.price) * 100)
    : 0;

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
        {/* Images Section */}
        <div>
          {/* Main Image */}
          <div className="relative h-96 lg:h-[500px] mb-4 rounded-lg overflow-hidden bg-gray-100">
            <Image
              src={product.images?.[selectedImage] || '/placeholder.jpg'}
              alt={product.name}
              fill
              className="object-cover"
            />
            {hasDiscount && (
              <div className="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-lg font-bold">
                {discountPercent}% OFF
              </div>
            )}
          </div>

          {/* Thumbnail Images */}
          {product.images && product.images.length > 1 && (
            <div className="grid grid-cols-4 gap-2">
              {product.images.map((image, index) => (
                <button
                  key={index}
                  onClick={() => setSelectedImage(index)}
                  className={`relative h-20 rounded overflow-hidden border-2 ${selectedImage === index ? 'border-blue-600' : 'border-gray-200'
                    }`}
                >
                  <Image
                    src={image}
                    alt={`${product.name} ${index + 1}`}
                    fill
                    className="object-cover"
                  />
                </button>
              ))}
            </div>
          )}
        </div>

        {/* Product Info Section */}
        <div>
          <h1 className="text-3xl font-bold mb-2">{product.name}</h1>

          <div className="flex items-center gap-2 mb-4">
            <span className="text-sm text-gray-500">{product.category}</span>
            {product.stock > 0 ? (
              <span className="text-sm text-green-600 font-semibold">In Stock</span>
            ) : (
              <span className="text-sm text-red-600 font-semibold">Out of Stock</span>
            )}
          </div>

          {/* Price */}
          <div className="flex items-center gap-3 mb-6">
            <span className="text-4xl font-bold text-blue-600">৳{displayPrice}</span>
            {hasDiscount && (
              <>
                <span className="text-2xl text-gray-400 line-through">৳{product.price}</span>
                <span className="bg-red-100 text-red-600 px-2 py-1 rounded text-sm font-semibold">
                  Save ৳{product.price - discountPrice}
                </span>
              </>
            )}
          </div>

          {/* Description */}
          <div className="mb-6">
            <h3 className="font-semibold mb-2">Description</h3>
            <p className="text-gray-600 leading-relaxed">{product.description}</p>
          </div>

          {/* Size Selection */}
          {product.sizes && product.sizes.length > 0 && (
            <div className="mb-6">
              <h3 className="font-semibold mb-2">Select Size</h3>
              <div className="flex gap-2">
                {product.sizes.map((size) => (
                  <button
                    key={size}
                    onClick={() => setSelectedSize(size)}
                    className={`px-4 py-2 border rounded-lg font-semibold ${selectedSize === size
                      ? 'bg-blue-600 text-white border-blue-600'
                      : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600'
                      }`}
                  >
                    {size}
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* Color Selection */}
          {product.colors && product.colors.length > 0 && (
            <div className="mb-6">
              <h3 className="font-semibold mb-2">Select Color</h3>
              <div className="flex gap-2">
                {product.colors.map((color) => (
                  <button
                    key={color}
                    onClick={() => setSelectedColor(color)}
                    className={`px-4 py-2 border rounded-lg font-semibold capitalize ${selectedColor === color
                      ? 'bg-blue-600 text-white border-blue-600'
                      : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600'
                      }`}
                  >
                    {color}
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* Quantity */}
          <div className="mb-6">
            <h3 className="font-semibold mb-2">Quantity</h3>
            <div className="flex items-center gap-3">
              <button
                onClick={() => setQuantity(Math.max(1, quantity - 1))}
                className="px-4 py-2 border rounded-lg hover:bg-gray-100"
              >
                -
              </button>
              <span className="text-xl font-semibold w-12 text-center">{quantity}</span>
              <button
                onClick={() => setQuantity(Math.min(product.stock, quantity + 1))}
                className="px-4 py-2 border rounded-lg hover:bg-gray-100"
              >
                +
              </button>
              <span className="text-sm text-gray-500">
                ({product.stock} available)
              </span>
            </div>
          </div>

          {/* Action Buttons */}
          <div className="flex gap-3 mb-6">
            <button
              onClick={handleAddToCart}
              disabled={product.stock === 0}
              className="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <FiShoppingCart size={20} />
              Add to Cart
            </button>
            <button className="px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-100">
              <FiHeart size={20} />
            </button>
            <button className="px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-100">
              <FiShare2 size={20} />
            </button>
          </div>

          {/* Additional Info */}
          <div className="border-t pt-6 space-y-2 text-sm">
            <div className="flex justify-between">
              <span className="text-gray-600">SKU:</span>
              <span className="font-semibold">{product._id || product.sku || product.id}</span>
            </div>
            {product.vendor && (
              <div className="flex justify-between">
                <span className="text-gray-600">Vendor:</span>
                <span className="font-semibold">{product.vendor.name || 'ClothStore'}</span>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Related Products */}
      {relatedProducts && relatedProducts.length > 0 && (
        <div>
          <h2 className="text-2xl font-bold mb-6">Related Products</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {relatedProducts.map((relatedProduct) => (
              <ProductCard key={relatedProduct._id || relatedProduct.id} product={relatedProduct} />
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
