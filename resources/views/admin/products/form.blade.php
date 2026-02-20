@extends('layouts.admin')

@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page-title', isset($product) ? 'Edit Product' : 'Add Product')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
          class="space-y-6">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl p-4 text-sm">
                @foreach($errors->all() as $err) <p>• {{ $err }}</p> @endforeach
            </div>
        @endif

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 space-y-5">

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="label">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required class="input" placeholder="e.g. Premium Cotton Shirt">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Description *</label>
                    <textarea name="description" rows="3" required class="input resize-none" placeholder="Product description...">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="label">Category *</label>
                    <select name="category" required class="input">
                        @foreach(['Shirt','T-Shirt','Pant','Jeans','Saree','Salwar','Kurti','Others'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $product->category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="input" placeholder="Auto-generated if empty">
                </div>
                <div>
                    <label class="label">Price (৳) *</label>
                    <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" required step="0.01" min="0" class="input">
                </div>
                <div>
                    <label class="label">Discount Price (৳)</label>
                    <input type="number" name="discount_price" value="{{ old('discount_price', $product->discount_price ?? '') }}" step="0.01" min="0" class="input" placeholder="Optional">
                </div>
                <div>
                    <label class="label">Stock Quantity *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required min="0" class="input">
                </div>
                <div>
                    <label class="label">Status</label>
                    <select name="is_active" class="input">
                        <option value="1" {{ old('is_active', $product->is_active ?? true) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $product->is_active ?? true) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="label">Sizes (comma separated)</label>
                    <input type="text" name="sizes" value="{{ old('sizes', is_array($product->sizes ?? null) ? implode(',', $product->sizes) : '') }}"
                           class="input" placeholder="S,M,L,XL">
                </div>
                <div>
                    <label class="label">Colors (comma separated)</label>
                    <input type="text" name="colors" value="{{ old('colors', is_array($product->colors ?? null) ? implode(',', $product->colors) : '') }}"
                           class="input" placeholder="Red,Blue,Black">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Image URLs (comma separated)</label>
                    <textarea name="images" rows="2" class="input resize-none" placeholder="https://images.unsplash.com/...">{{ old('images', is_array($product->images ?? null) ? implode(', ', $product->images) : '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary px-8 py-3">
                {{ isset($product) ? 'Update Product' : 'Create Product' }}
            </button>
            <a href="{{ route('admin.products') }}" class="btn-outline px-8 py-3">Cancel</a>
        </div>
    </form>
</div>
@endsection
