@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- Back --}}
    <a href="/" class="inline-flex items-center gap-1.5 text-gray-400 hover:text-gray-700 mb-6 text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to listings
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2">

            {{-- ═══ IMAGE ═══ --}}
            <div class="relative bg-gray-50 min-h-72 md:min-h-96 flex items-center justify-center overflow-hidden">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                         alt="{{ $product->title }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="flex flex-col items-center gap-3 text-gray-300">
                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm">No image</span>
                    </div>
                @endif

                {{-- Status Badge --}}
                @if($product->status !== 'active')
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                        <span class="bg-white text-gray-800 font-bold px-6 py-2 rounded-full text-sm uppercase tracking-wide">
                            {{ $product->status }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- ═══ DETAILS ═══ --}}
            <div class="p-6 lg:p-8 flex flex-col">
                <div class="flex-1">

                    {{-- Category + Views --}}
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full border"
                              style="background:var(--orange-light);color:var(--orange);border-color:var(--orange-border)">
                            {{ $product->category->name }}
                        </span>
                        <span class="flex items-center gap-1 text-gray-400 text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ number_format($product->views) }} views
                        </span>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-2xl font-extrabold text-gray-900 mb-3 leading-tight">
                        {{ $product->title }}
                    </h1>

                    {{-- Price --}}
                    <div class="flex items-baseline gap-2 mb-5">
                        <span class="text-4xl font-extrabold" style="color:var(--orange)">
                            ₱{{ number_format($product->price, 2) }}
                        </span>
                        @if($product->stock > 0 && $product->stock <= 5)
                            <span class="text-xs text-red-500 font-semibold">
                                Only {{ $product->stock }} left!
                            </span>
                        @endif
                    </div>

                    {{-- Description --}}
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">
                        {{ $product->description }}
                    </p>

                    {{-- Meta Info --}}
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 mb-0.5">Location</p>
                            <p class="text-sm font-semibold text-gray-700">
                                📍 {{ $product->location ?? 'Not specified' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 mb-0.5">Available</p>
                            <p class="text-sm font-semibold text-gray-700">
                                📦 {{ $product->stock }} in stock
                            </p>
                        </div>
                    </div>

                    {{-- Seller Info --}}
                    <div class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 mb-6">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                             style="background:var(--orange)">
                            {{ strtoupper(substr($product->seller->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400">Listed by</p>
                            <p class="text-sm font-semibold text-gray-800 truncate">
                                {{ $product->seller->name }}
                            </p>
                        </div>
                        <span class="text-xs text-gray-400">
                            {{ $product->created_at->diffForHumans() }}
                        </span>
                    </div>

                </div>

                {{-- ═══ ACTION BUTTONS ═══ --}}
                <div class="space-y-3">
                    @auth
                        @if(auth()->user()->isBuyer())
                            @if($product->isAvailable())

                                {{-- Order Form --}}
                                <form action="/orders" method="POST" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <div class="flex items-center gap-3">
                                        <label class="text-sm font-medium text-gray-600 flex-shrink-0">
                                            Qty
                                        </label>
                                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                                            <button type="button"
                                                    onclick="this.nextElementSibling.stepDown()"
                                                    class="px-3 py-2 text-gray-500 hover:bg-gray-50 text-lg font-light">−</button>
                                            <input type="number"
                                                   name="quantity"
                                                   value="1"
                                                   min="1"
                                                   max="{{ $product->stock }}"
                                                   class="w-16 text-center py-2 text-sm font-semibold border-x border-gray-200 focus:outline-none">
                                            <button type="button"
                                                    onclick="this.previousElementSibling.stepUp()"
                                                    class="px-3 py-2 text-gray-500 hover:bg-gray-50 text-lg font-light">+</button>
                                        </div>
                                        <span class="text-xs text-gray-400">
                                            Max: {{ $product->stock }}
                                        </span>
                                    </div>

                                    <button type="submit"
                                            class="btn-orange w-full py-3.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Place Order
                                    </button>
                                </form>

                                {{-- Favorite --}}
                                <form action="/favorites/{{ $product->id }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full py-3 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:border-red-300 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                        Save to Wishlist
                                    </button>
                                </form>

                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center">
                                    <p class="text-gray-500 font-semibold text-sm">Out of Stock</p>
                                    <p class="text-gray-400 text-xs mt-1">This item is no longer available</p>
                                </div>
                            @endif

                        @elseif(auth()->user()->isSeller() && $product->seller_id === auth()->id())
                            {{-- Own product --}}
                            <a href="/seller/products/{{ $product->id }}/edit"
                               class="btn-orange w-full py-3.5 rounded-xl font-bold text-sm text-center block">
                                Edit My Listing
                            </a>
                            <form action="/seller/products/{{ $product->id }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Are you sure?')"
                                        class="w-full py-3 rounded-xl font-semibold text-sm border border-red-200 text-red-500 hover:bg-red-50 transition-all">
                                    Delete Listing
                                </button>
                            </form>
                        @else
                            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-center">
                                <p class="text-orange-700 text-sm font-medium">Switch to buyer account to order</p>
                            </div>
                        @endif

                    @else
                        <a href="/login"
                           class="btn-orange w-full py-3.5 rounded-xl font-bold text-sm text-center block">
                            Login to Order
                        </a>
                        <a href="/register"
                           class="w-full py-3 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all text-center block">
                            Create Account
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </div>

</div>

@endsection