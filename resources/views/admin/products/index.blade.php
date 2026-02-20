@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="flex items-center justify-between mb-5">
    <form method="GET" action="{{ route('admin.products') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
               class="input text-sm w-56">
        <button type="submit" class="btn-primary text-sm px-4">Search</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn-primary">+ Add Product</a>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-head">Product</th>
                    <th class="table-head">Category</th>
                    <th class="table-head">Price</th>
                    <th class="table-head">Stock</th>
                    <th class="table-head">Status</th>
                    <th class="table-head">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-800 rounded-lg overflow-hidden flex-shrink-0">
                                    @if(is_array($product->images) && count($product->images) > 0)
                                        <img src="{{ $product->images[0] }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-sm">👔</div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ Str::limit($product->name, 35) }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->sku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell">{{ $product->category }}</td>
                        <td class="table-cell">
                            <p class="text-white font-medium">৳{{ number_format($product->price) }}</p>
                            @if($product->discount_price)
                                <p class="text-xs text-emerald-400">Sale: ৳{{ number_format($product->discount_price) }}</p>
                            @endif
                        </td>
                        <td class="table-cell">
                            <span class="{{ $product->stock <= 5 ? 'text-red-400' : 'text-gray-300' }} font-medium">{{ $product->stock }}</span>
                        </td>
                        <td class="table-cell">
                            <span class="badge {{ $product->is_active ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-gray-500/20 text-gray-400 border-gray-500/30' }} border">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="table-cell">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-indigo-400 hover:underline text-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}"
                                      onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:underline text-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="table-cell text-center text-gray-600 py-12">No products found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5 border-t border-gray-800">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
