@extends('layouts.admin')
@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page-title', isset($product) ? 'Edit Product' : 'Add Product')

@section('content')
<div class="max-w-3xl">
    <form method="POST"
          action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
          enctype="multipart/form-data"
          class="space-y-6"
          x-data="productForm()">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="text-red-500 text-xs font-bold uppercase tracking-widest p-4 pb-0 space-y-1">
                @foreach($errors->all() as $err)
                    <p>• {{ $err }}</p>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="text-[#C9A84C] text-xs font-bold uppercase tracking-widest p-4 pb-0">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Core Details --}}
        <div class="bg-white border border-gray-100 shadow-sm rounded-none p-6 sm:p-8 space-y-6">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3 mb-6">Product Details</h2>

            <div class="grid sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Product Name <span class="text-[#C9A84C]">*</span></label>
                    <input type="text" name="name"
                           value="{{ old('name', $product->name ?? '') }}"
                           required class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors" placeholder="e.g. Premium Cotton Shirt">
                </div>

                <div class="sm:col-span-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Description <span class="text-[#C9A84C]">*</span></label>
                    <textarea name="description" rows="3" required
                              class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors resize-none"
                              placeholder="Product description...">{{ old('description', $product->description ?? '') }}</textarea>
                </div>

                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Category <span class="text-[#C9A84C]">*</span></label>
                    <select name="category" required class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors">
                        @foreach(['Shirt','T-Shirt','Pant','Jeans','Saree','Salwar','Kurti','Jacket','Dress','Others'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $product->category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">SKU</label>
                    <input type="text" name="sku"
                           value="{{ old('sku', $product->sku ?? '') }}"
                           class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors" placeholder="Auto-generated if empty">
                </div>

                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Price (৳) <span class="text-[#C9A84C]">*</span></label>
                    <input type="number" name="price"
                           value="{{ old('price', $product->price ?? '') }}"
                           required step="0.01" min="0" class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors" placeholder="0.00">
                </div>

                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Discount Price (৳)</label>
                    <input type="number" name="discount_price"
                           value="{{ old('discount_price', $product->discount_price ?? '') }}"
                           step="0.01" min="0" class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors" placeholder="Leave blank for no discount">
                </div>

                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Stock Quantity <span class="text-[#C9A84C]">*</span></label>
                    <input type="number" name="stock"
                           value="{{ old('stock', $product->stock ?? 0) }}"
                           required min="0" class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors">
                </div>

                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-2 block">Status <span class="text-[#C9A84C]">*</span></label>
                    <select name="is_active" required class="w-full bg-white border border-gray-200 px-4 py-2.5 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors">
                        <option value="1" {{ old('is_active', $product->is_active ?? '1') == '1' ? 'selected' : '' }}>Active (Visible)</option>
                        <option value="0" {{ old('is_active', $product->is_active ?? '1') == '0' ? 'selected' : '' }}>Draft (Hidden)</option>
                    </select>
                </div>

            </div>
        </div>

        {{-- Variant Builder Section --}}
        <div class="bg-white border border-gray-100 shadow-sm rounded-none p-6 sm:p-8 space-y-6">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3">Product Variants</h2>
            <div class="space-y-5">
                {{-- Sizes checkboxes --}}
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-3 block">Available Sizes</label>
                    <div class="flex flex-wrap gap-4">
                        <template x-for="size in ['S', 'M', 'L', 'XL', 'XXL']" :key="size">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" :value="size" x-model="selectedSizes" @change="generateVariants()" class="w-4 h-4 text-[#C9A84C] focus:ring-[#C9A84C] border-gray-300 rounded">
                                <span x-text="size" class="text-sm text-[#1A1A1A] font-bold"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Colors picker --}}
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-3 block">Available Colors</label>
                    <div class="flex items-center gap-2 mb-3">
                        <input type="text" x-model="newColor" @keydown.enter.prevent="addColor" class="bg-white border border-gray-200 px-4 py-2 text-sm text-[#1A1A1A] focus:outline-none focus:border-[#C9A84C] transition-colors flex-1" placeholder="e.g. Red, Blue, #FFFFFF">
                        <button type="button" @click="addColor" class="bg-[#1A1A1A] text-white px-4 py-2 text-[10px] font-bold uppercase tracking-widest hover:bg-[#333] transition-colors">Add</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(color, index) in colors" :key="index">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 border border-gray-200 text-xs font-bold uppercase text-[#1A1A1A]">
                                <span x-text="color"></span>
                                <button type="button" @click="removeColor(index)" class="text-gray-400 hover:text-red-500 transition-colors ml-1">&times;</button>
                            </span>
                        </template>
                    </div>
                </div>

                {{-- Generated Variant Grid --}}
                <div x-show="variants.length > 0" class="mt-6 border-t border-gray-100 pt-6">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-4 block">Adjust Variant Details</label>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Size</th>
                                    <th class="py-3 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Color</th>
                                    <th class="py-3 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Stock</th>
                                    <th class="py-3 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">SKU</th>
                                    <th class="py-3 px-4 text-[9px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-200">Price Modifier (৳)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template x-for="(v, idx) in variants" :key="v.size + '-' + v.color">
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 text-xs font-bold text-[#1A1A1A]" x-text="v.size || 'N/A'"></td>
                                        <td class="py-2 px-4 text-xs font-bold text-[#1A1A1A]" x-text="v.color || 'N/A'"></td>
                                        <td class="py-2 px-4">
                                            <input type="number" min="0" x-model="v.stock" :name="'variants['+idx+'][stock]'" class="w-20 bg-white border border-gray-200 px-2 py-1 text-xs focus:outline-none focus:border-[#C9A84C]">
                                        </td>
                                        <td class="py-2 px-4">
                                            <input type="text" x-model="v.sku" :name="'variants['+idx+'][sku]'" placeholder="Auto" class="w-24 bg-white border border-gray-200 px-2 py-1 text-xs focus:outline-none focus:border-[#C9A84C]">
                                        </td>
                                        <td class="py-2 px-4">
                                            <input type="number" step="0.01" x-model="v.price_modifier" :name="'variants['+idx+'][price_modifier]'" class="w-24 bg-white border border-gray-200 px-2 py-1 text-xs focus:outline-none focus:border-[#C9A84C]">
                                            <!-- Hidden necessary mapped fields -->
                                            <input type="hidden" :name="'variants['+idx+'][size]'" :value="v.size">
                                            <input type="hidden" :name="'variants['+idx+'][color]'" :value="v.color">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Image Upload Section --}}
        <div class="bg-white border border-gray-100 shadow-sm rounded-none p-6 sm:p-8">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#1A1A1A] border-l-2 border-[#C9A84C] pl-3 mb-6">Product Images</h2>

            {{-- Existing images (edit mode) --}}
            @if(isset($product) && is_array($product->images) && count($product->images) > 0)
            <div class="mb-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-3 block">Current Images <span class="text-gray-400 font-normal normal-case tracking-normal">(check box to delete)</span></p>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    @foreach($product->images as $i => $imgUrl)
                    <div class="relative group">
                        <img src="{{ $imgUrl }}" alt="Product image {{ $i + 1 }}"
                             class="w-full aspect-square object-cover border border-gray-200">
                        {{-- First image badge --}}
                        @if($i === 0)
                            <span class="absolute top-1.5 left-1.5 bg-[#1A1A1A] text-[#C9A84C] text-[9px] uppercase tracking-widest font-bold px-2 py-0.5 shadow-sm border border-[#C9A84C]/30">Main</span>
                        @endif
                        {{-- Delete checkbox --}}
                        <label class="absolute top-1.5 right-1.5 cursor-pointer">
                            <input type="checkbox" name="delete_images[]" value="{{ $imgUrl }}"
                                   class="sr-only peer">
                            <div class="w-6 h-6 bg-white border border-gray-300 flex items-center justify-center
                                        peer-checked:bg-white peer-checked:border-red-500 transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5 text-red-500 opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
                <p class="text-[10px] text-red-500 mt-2 font-bold uppercase tracking-widest">⚠️ Checked images will be permanently deleted on save.</p>
            </div>
            @endif

            {{-- Upload new images --}}
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-[#1A1A1A] mb-3 block">
                    {{ isset($product) ? 'Upload Additional Images' : 'Upload Images' }}
                    <span class="text-gray-400 font-normal normal-case tracking-normal">(JPG, PNG, WEBP — max 4MB each)</span>
                </p>

                {{-- Drop zone --}}
                <label for="image-upload"
                       class="flex flex-col items-center justify-center border-2 border-dashed border-[#C9A84C] bg-white p-8 cursor-pointer hover:bg-gray-50 transition-colors group"
                       @dragover.prevent
                       @drop.prevent="handleDrop($event)">
                    <svg class="w-10 h-10 text-[#C9A84C] group-hover:text-[#b08a38] transition-colors mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-[#1A1A1A] text-xs font-bold uppercase tracking-widest group-hover:text-black transition-colors">
                        Click to browse or drag & drop images
                    </p>
                    <p class="text-gray-500 text-[10px] uppercase tracking-widest mt-1">First image will be the main product image</p>
                    <input id="image-upload" type="file" name="images[]" multiple accept="image/*"
                           class="sr-only"
                           @change="previewImages($event.target.files)">
                </label>

                {{-- New image previews --}}
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-4" x-show="previews.length > 0">
                    <template x-for="(src, i) in previews" :key="i">
                        <div class="relative group">
                            <img :src="src" class="w-full aspect-square object-cover border border-gray-200">
                            <span x-show="i === 0 && {{ isset($product) && count($product->images ?? []) > 0 ? 'false' : 'true' }}"
                                  class="absolute top-1.5 left-1.5 bg-[#1A1A1A] text-[#C9A84C] text-[9px] uppercase tracking-widest font-bold px-2 py-0.5 shadow-sm border border-[#C9A84C]/30">Main</span>
                            <button type="button" @click="removePreview(i)"
                                    class="absolute top-1.5 right-1.5 w-6 h-6 bg-white border border-red-500 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm">
                                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-[#C9A84C] hover:bg-[#b08a38] text-white text-[11px] font-bold uppercase tracking-[0.2em] px-8 py-3 transition-colors shadow-sm">
                {{ isset($product) ? 'Update Product' : 'Create Product' }}
            </button>
            <a href="{{ route('admin.products') }}" class="bg-white border border-gray-200 text-[#1A1A1A] hover:bg-gray-50 text-[11px] font-bold uppercase tracking-[0.2em] px-8 py-3 transition-colors text-center flex items-center shadow-sm">Cancel</a>
        </div>
    </form>
</div>

<script>
function productForm() {
    return {
        // Image manager logic
        previews: [],
        files: [],

        // Variants Builder logic
        variants: @json(isset($product) ? $product->variants : []),
        selectedSizes: @json($product->sizes ?? []),
        colors: @json($product->colors ?? []),
        newColor: '',

        init() {
            // Re-generate table initially if editing
            if (this.variants.length === 0 && (this.selectedSizes.length > 0 || this.colors.length > 0)) {
                this.generateVariants();
            }
        },

        addColor() {
            const c = this.newColor.trim();
            if (c && !this.colors.includes(c)) {
                this.colors.push(c);
                this.newColor = '';
                this.generateVariants();
            }
        },
        removeColor(index) {
            this.colors.splice(index, 1);
            this.generateVariants();
        },
        generateVariants() {
            let existingMap = {};
            this.variants.forEach(v => {
                existingMap[(v.size || '') + '|' + (v.color || '')] = v;
            });

            if (this.selectedSizes.length === 0 && this.colors.length === 0) {
                this.variants = [];
                return;
            }

            let newVariants = [];
            let loopSizes = this.selectedSizes.length > 0 ? this.selectedSizes : [null];
            let loopColors = this.colors.length > 0 ? this.colors : [null];

            loopSizes.forEach(s => {
                loopColors.forEach(c => {
                    let key = (s || '') + '|' + (c || '');
                    if (existingMap[key]) {
                        newVariants.push(existingMap[key]);
                    } else {
                        newVariants.push({
                            size: s,
                            color: c,
                            stock: 0,
                            sku: '',
                            price_modifier: 0
                        });
                    }
                });
            });

            this.variants = newVariants;
        },

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
