@extends('layouts.app')

@section('content')

{{-- ═══════════════════════════════════════════ --}}
{{-- HERO BANNER (homepage only) --}}
{{-- ═══════════════════════════════════════════ --}}
@unless(isset($query))
<div class="rounded-2xl p-8 mb-8 relative overflow-hidden" style="background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);">
    <div class="relative z-10">
        <h1 class="text-3xl font-extrabold text-white mb-2">
            Buy & Sell Locally 🛒
        </h1>
        <p class="text-orange-100 text-sm mb-5 max-w-md">
            Find great deals on products near you. Trusted sellers, real products.
        </p>

        {{-- Mobile Search --}}
        <form action="/search" method="GET" class="flex gap-2 sm:hidden">
            <input type="text" name="q" placeholder="Search products..."
                   class="flex-1 px-4 py-2.5 rounded-xl text-sm focus:outline-none">
            <button type="submit"
                    class="bg-white text-orange-600 px-4 py-2.5 rounded-xl text-sm font-semibold">
                Search
            </button>
        </form>
    </div>

    {{-- Decorative circles --}}
    <div class="absolute -right-8 -top-8 w-40 h-40 bg-white opacity-5 rounded-full"></div>
    <div class="absolute -right-4 -bottom-10 w-60 h-60 bg-white opacity-5 rounded-full"></div>
</div>
@endunless

{{-- ═══════════════════════════════════════════ --}}
{{-- SEARCH HEADER (search results only) --}}
{{-- ═══════════════════════════════════════════ --}}
@if(isset($query))
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">
        Results for <span style="color:var(--orange)">"{{ $query }}"</span>
    </h1>
    <p class="text-gray-500 text-sm mt-1">{{ $products->total() }} products found</p>
</div>
@endif

{{-- ═══════════════════════════════════════════ --}}
{{-- SECTION HEADER (homepage) --}}
{{-- ═══════════════════════════════════════════ --}}
@unless(isset($query))
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-lg font-bold text-gray-800">Latest Listings</h2>
        <p class="text-gray-400 text-sm">{{ $products->total() }} products available</p>
    </div>
</div>
@endunless

{{-- ═══════════════════════════════════════════ --}}
{{-- PRODUCTS GRID --}}
{{-- ═══════════════════════════════════════════ --}}
@forelse($products as $product)
    @if($loop->first)
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
    @endif

    <a href="/products/{{ $product->slug }}"
       class="bg-white rounded-2xl border border-gray-100 overflow-hidden card-hover group">

        {{-- Image --}}
        <div class="relative h-44 bg-gray-50 overflow-hidden">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                     alt="{{ $product->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full flex flex-col items-center justify-center gap-2">
                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs text-gray-300">No image</span>
                </div>
            @endif

            {{-- Category Badge --}}
            <div class="absolute top-2 left-2">
                <span class="bg-white/90 backdrop-blur-sm text-gray-600 text-xs px-2 py-0.5 rounded-full border border-gray-200 font-medium">
                    {{ $product->category->name }}
                </span>
            </div>
        </div>

        {{-- Info --}}
        <div class="p-3">
            <h3 class="text-sm font-semibold text-gray-800 truncate leading-snug">
                {{ $product->title }}
            </h3>

            <p class="text-base font-extrabold mt-1.5 mb-2" style="color:var(--orange)">
                ₱{{ number_format($product->price, 2) }}
            </p>

            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-400 truncate flex-1">
                    {{ $product->seller->name }}
                </p>
                <div class="flex items-center gap-0.5 text-gray-300 text-xs">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ number_format($product->views) }}
                </div>
            </div>

            @if($product->location)
            <p class="text-xs text-gray-300 mt-1 truncate">
                📍 {{ $product->location }}
            </p>
            @endif
        </div>
    </a>

    @if($loop->last)
    </div>
    @endif

@empty
    <div class="text-center py-24 col-span-full">
        <div class="w-20 h-20 rounded-full bg-orange-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10" style="color:var(--orange)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <h3 class="text-gray-600 font-semibold text-lg mb-1">
            @if(isset($query))
                No results for "{{ $query }}"
            @else
                No listings yet
            @endif
        </h3>
        <p class="text-gray-400 text-sm">
            @if(isset($query))
                Try a different keyword
            @else
                Be the first to list a product!
            @endif
        </p>
    </div>
@endforelse

{{-- Pagination --}}
@if($products->hasPages())
    <div class="mt-10 flex justify-center">
        {{ $products->links() }}
    </div>
@endif

@endsection