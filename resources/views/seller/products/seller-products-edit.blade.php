@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <a href="/seller/products"
           class="inline-flex items-center gap-1.5 text-gray-400 hover:text-gray-700 mb-3 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            My Listings
        </a>
        <h1 class="text-2xl font-extrabold text-gray-900">Edit Listing</h1>
        <p class="text-gray-400 text-sm mt-0.5">Update your product details</p>
    </div>

    {{-- Form --}}
    <form action="/seller/products/{{ $product->id }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @csrf
        @method('PATCH')

        {{-- Basic Info --}}
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">
                Product Info
            </h2>

            {{-- Title --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Product Title <span class="text-red-400">*</span>
                </label>
                <input type="text"
                       name="title"
                       value="{{ old('title', $product->title) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all @error('title') border-red-300 bg-red-50 @enderror"
                       style="--tw-ring-color: #F97316">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Description <span class="text-red-400">*</span>
                </label>
                <textarea name="description"
                          rows="4"
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-transparent resize-none transition-all @error('description') border-red-300 bg-red-50 @enderror"
                          style="--tw-ring-color: #F97316">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Category --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Category <span class="text-red-400">*</span>
                </label>
                <select name="category_id"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all"
                        style="--tw-ring-color: #F97316">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Status
                </label>
                <select name="status"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all"
                        style="--tw-ring-color: #F97316">
                    <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>
                        Active — visible to buyers
                    </option>
                    <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>
                        Inactive — hidden from buyers
                    </option>
                </select>
            </div>
        </div>

        {{-- Pricing & Stock --}}
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">
                Pricing & Stock
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Price (₱) <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold">₱</span>
                        <input type="number"
                               name="price"
                               value="{{ old('price', $product->price) }}"
                               min="1" step="0.01"
                               class="w-full pl-8 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all @error('price') border-red-300 bg-red-50 @enderror"
                               style="--tw-ring-color: #F97316">
                    </div>
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Stock <span class="text-red-400">*</span>
                    </label>
                    <input type="number"
                           name="stock"
                           value="{{ old('stock', $product->stock) }}"
                           min="0"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all @error('stock') border-red-300 bg-red-50 @enderror"
                           style="--tw-ring-color: #F97316">
                    @error('stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Location & Image --}}
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">
                Location & Photo
            </h2>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Location</label>
                <input type="text"
                       name="location"
                       value="{{ old('location', $product->location) }}"
                       placeholder="e.g. Quezon City, Metro Manila"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-transparent transition-all"
                       style="--tw-ring-color: #F97316">
            </div>

            {{-- Current Image --}}
            @if($product->image)
                <div class="mb-4">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Current Photo</p>
                    <div class="relative w-32 h-32 rounded-xl overflow-hidden border border-gray-200">
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->title }}"
                             class="w-full h-full object-cover">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Upload a new photo to replace this</p>
                </div>
            @endif

            {{-- New Image --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    {{ $product->image ? 'Replace Photo' : 'Product Photo' }}
                </label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-orange-300 transition-colors">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-400 mb-2">JPG, PNG up to 2MB</p>
                    <input type="file"
                           name="image"
                           accept="image/jpg,image/jpeg,image/png"
                           class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:text-white cursor-pointer"
                           style="file:background:var(--orange)">
                </div>
                @error('image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Submit --}}
        <div class="p-6 bg-gray-50 flex items-center justify-between gap-4">
            <a href="/seller/products"
               class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                Cancel
            </a>
            <div class="flex items-center gap-3">
                {{-- Delete --}}
                <form action="/seller/products/{{ $product->id }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this listing?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl font-semibold text-sm border border-red-200 text-red-500 hover:bg-red-50 transition-all">
                        Delete
                    </button>
                </form>

                {{-- Save --}}
                <button type="submit"
                        class="btn-orange px-8 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>

    </form>
</div>

@endsection