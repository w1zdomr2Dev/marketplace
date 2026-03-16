@extends('layouts.app')

@section('content')

    {{-- Page Title --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Browse Products</h1>
        <p class="text-gray-500">Find what you're looking for</p>
    </div>

    {{-- Products Grid --}}
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <a href="/products/{{ $product->slug }}" class="bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden">

                    {{-- Product Image --}}
                    <div class="h-48 bg-gray-200 flex items-center justify-center">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-gray-400 text-4xl">📦</span>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="p-4">
                        <h2 class="font-semibold text-gray-800 truncate">{{ $product->title }}</h2>
                        <p class="text-blue-600 font-bold mt-1">₱{{ number_format($product->price, 2) }}</p>
                        <p class="text-gray-400 text-sm mt-1">{{ $product->location }}</p>
                        <p class="text-gray-400 text-xs mt-1">{{ $product->seller->name }}</p>
                    </div>

                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $products->links() }}
        </div>

    @else
        <div class="text-center py-20">
            <span class="text-6xl">🛒</span>
            <p class="text-gray-500 mt-4">Walang products pa.</p>
        </div>
    @endif

@endsection