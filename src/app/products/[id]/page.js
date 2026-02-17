import ProductDetail from '@/components/products/ProductDetail';

async function getProduct(id) {
  try {
    const res = await fetch(`${process.env.NEXTAUTH_URL}/api/products/${id}`, {
      cache: 'no-store',
    });
    
    if (!res.ok) return null;
    
    const result = await res.json();
    return result.data;
  } catch (error) {
    console.error('Error fetching product:', error);
    return null;
  }
}

async function getRelatedProducts(category) {
  try {
    const res = await fetch(`${process.env.NEXTAUTH_URL}/api/products?category=${category}`, {
      cache: 'no-store',
    });
    
    if (!res.ok) return [];
    
    const result = await res.json();
    return result.data.slice(0, 4);
  } catch (error) {
    console.error('Error fetching related products:', error);
    return [];
  }
}

export default async function ProductDetailPage({ params }) {
  const product = await getProduct(params.id);
  
  if (!product) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <h1 className="text-2xl font-bold mb-4">Product Not Found</h1>
        <p className="text-gray-500">The product you're looking for doesn't exist.</p>
      </div>
    );
  }

  const relatedProducts = await getRelatedProducts(product.category);

  return <ProductDetail product={product} relatedProducts={relatedProducts} />;
}
