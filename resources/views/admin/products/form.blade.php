@extends('layouts.admin')
@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page-title', isset($product) ? 'Edit Product' : 'Add Product')

@section('content')
<div class="max-w-3xl">
    <form method="POST"
          action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
          enctype="multipart/form-data"
          class="space-y-6"
          x-data="imageManager()">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl p-4 text-sm space-y-1">
                @foreach($errors->all() as $err)
                    <p>• {{ $err }}</p>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl p-4 text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Core Details --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 space-y-5">
            <h2 class="text-base font-semibold text-white border-b border-gray-800 pb-3">Product Details</h2>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="label">Product Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name"
                           value="{{ old('name', $product->name ?? '') }}"
                           required class="input" placeholder="e.g. Premium Cotton Shirt">
                </div>

                <div class="sm:col-span-2">
                    <label class="label">Description <span class="text-red-400">*</span></label>
                    <textarea name="description" rows="3" required
                              class="input resize-none"
                              placeholder="Product description...">{{ old('description', $product->description ?? '') }}</textarea>
                </div>

                <div>
                    <label class="label">Category <span class="text-red-400">*</span></label>
                    <select name="category" required class="input">
                        @foreach(['Shirt','T-Shirt','Pant','Jeans','Saree','Salwar','Kurti','Jacket','Dress','Others'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $product->category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label">SKU</label>
                    <input type="text" name="sku"
                           value="{{ old('sku', $product->sku ?? '') }}"
                           class="input" placeholder="Auto-generated if empty">
                </div>

                <div>
                    <label class="label">Price (৳) <span class="text-red-400">*</span></label>
                    <input type="number" name="price"
                           value="{{ old('price', $product->price ?? '') }}"
                           required step="0.01" min="0" class="input" placeholder="0.00">
                </div>

                <div>
                    <label class="label">Discount Price (৳)</label>
                    <input type="number" name="discount_price"
                           value="{{ old('discount_price', $product->discount_price ?? '') }}"
                           step="0.01" min="0" class="input" placeholder="Leave blank for no discount">
                </div>

                <div>
                    <label class="label">Stock Quantity <span class="text-red-400">*</span></label>
                    <input type="number" name="stock"
                           value="{{ old('stock', $product->stock ?? 0) }}"
                           required min="0" class="input">
                </div>

                <div>
                    <label class="label">Status</label>
                    <select name="is_active" class="input">
                        <option value="1" {{ old('is_active', $product->is_active ?? true) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $product->is_active ?? true) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="label">Sizes <span class="text-gray-500 font-normal text-xs">(comma separated)</span></label>
                    <input type="text" name="sizes"
                           value="{{ old('sizes', is_array($product->sizes ?? null) ? implode(',', $product->sizes) : '') }}"
                           class="input" placeholder="S,M,L,XL,XXL">
                </div>

                <div>
                    <label class="label">Colors <span class="text-gray-500 font-normal text-xs">(comma separated)</span></label>
                    <input type="text" name="colors"
                           value="{{ old('colors', is_array($product->colors ?? null) ? implode(',', $product->colors) : '') }}"
                           class="input" placeholder="Red,Blue,Black,White">
                </div>
            </div>
        </div>

        {{-- Image Upload Section --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
            <h2 class="text-base font-semibold text-white border-b border-gray-800 pb-3 mb-5">Product Images</h2>

            {{-- Existing images (edit mode) --}}
            @if(isset($product) && is_array($product->images) && count($product->images) > 0)
            <div class="mb-5">
                <p class="label mb-3">Current Images <span class="text-gray-500 font-normal text-xs">(check box to delete)</span></p>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    @foreach($product->images as $i => $imgUrl)
                    <div class="relative group">
                        <img src="{{ $imgUrl }}" alt="Product image {{ $i + 1 }}"
                             class="w-full aspect-square object-cover rounded-xl border border-gray-700">
                        {{-- First image badge --}}
                        @if($i === 0)
                            <span class="absolute top-1.5 left-1.5 bg-indigo-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-md">Main</span>
                        @endif
                        {{-- Delete checkbox --}}
                        <label class="absolute top-1.5 right-1.5 cursor-pointer">
                            <input type="checkbox" name="delete_images[]" value="{{ $imgUrl }}"
                                   class="sr-only peer">
                            <div class="w-6 h-6 rounded-full bg-gray-900/80 border border-gray-600 flex items-center justify-center
                                        peer-checked:bg-red-500 peer-checked:border-red-400 transition-all">
                                <svg class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-600 mt-2">⚠️ Checked images will be permanently deleted on save.</p>
            </div>
            @endif

            {{-- Upload new images --}}
            <div>
                <p class="label mb-3">
                    {{ isset($product) ? 'Upload Additional Images' : 'Upload Images' }}
                    <span class="text-gray-500 font-normal text-xs">(JPG, PNG, WEBP — max 4MB each)</span>
                </p>

                {{-- Drop zone --}}
                <label for="image-upload"
                       class="flex flex-col items-center justify-center border-2 border-dashed border-gray-700 rounded-2xl p-8 cursor-pointer hover:border-indigo-500 transition-colors group"
                       @dragover.prevent
                       @drop.prevent="handleDrop($event)">
                    <svg class="w-10 h-10 text-gray-600 group-hover:text-indigo-400 transition-colors mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-400 text-sm font-medium group-hover:text-indigo-400 transition-colors">
                        Click to browse or drag & drop images
                    </p>
                    <p class="text-gray-600 text-xs mt-1">First image will be the main product image</p>
                    <input id="image-upload" type="file" name="images[]" multiple accept="image/*"
                           class="sr-only"
                           @change="previewImages($event.target.files)">
                </label>

                {{-- New image previews --}}
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-4" x-show="previews.length > 0">
                    <template x-for="(src, i) in previews" :key="i">
                        <div class="relative group">
                            <img :src="src" class="w-full aspect-square object-cover rounded-xl border border-gray-700">
                            <span x-show="i === 0 && {{ isset($product) && count($product->images ?? []) > 0 ? 'false' : 'true' }}"
                                  class="absolute top-1.5 left-1.5 bg-indigo-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-md">Main</span>
                            <button type="button" @click="removePreview(i)"
                                    class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-red-500 border border-red-400 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="submit" class="btn-primary px-8 py-3">
                {{ isset($product) ? 'Update Product' : 'Create Product' }}
            </button>
            <a href="{{ route('admin.products') }}" class="btn-outline px-8 py-3">Cancel</a>
        </div>
    </form>
</div>

<script>
function imageManager() {
    return {
        previews: [],
        files: [],

        previewImages(fileList) {
            this.files = Array.from(fileList);
            this.previews = [];
            this.files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },

        removePreview(index) {
            this.previews.splice(index, 1);
            this.files.splice(index, 1);
            // Rebuild the file input with remaining files
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f));
            document.getElementById('image-upload').files = dt.files;
        },

        handleDrop(e) {
            const files = e.dataTransfer.files;
            const input = document.getElementById('image-upload');
            const dt = new DataTransfer();
            Array.from(files).forEach(f => dt.items.add(f));
            input.files = dt.files;
            this.previewImages(files);
        }
    };
}
</script>
@endsection
