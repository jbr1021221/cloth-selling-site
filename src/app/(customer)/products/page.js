import ProductGrid from '@/components/products/ProductGrid';

async function getProducts(searchParams) {
  try {
    const params = new URLSearchParams();
    
    if (searchParams.category) {
      params.append('category', searchParams.category);
    }
    
    if (searchParams.search) {
      params.append('search', searchParams.search);
    }
    
    const res = await fetch(`${process.env.NEXTAUTH_URL}/api/products?${params}`, {
      cache: 'no-store',
    });
    
    if (!res.ok) return { data: [] };
    
    const result = await res.json();
    return result;
  } catch (error) {
    console.error('Error:', error);
    return { data: [] };
  }
}

export default async function ProductsPage({ searchParams }) {
  const resolvedSearchParams = await searchParams;
  const { data: products } = await getProducts(resolvedSearchParams);

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-3xl font-bold mb-8">All Products</h1>
      <ProductGrid products={products} />
    </div>
  );
}