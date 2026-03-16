@extends('layouts.app')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">My Listings</h1>
        <p class="text-gray-400 text-sm mt-0.5">{{ $products->total() }} products listed</p>
    </div>
    <a href="/seller/products/create"
       class="btn-orange px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Listing
    </a>
</div>

{{-- Products Table --}}
@forelse($products as $product)
    @if($loop->first)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left text-xs font-bold text-gray-400 uppercase tracking-wide px-6 py-4">Product</th>
                    <th class="text-left text-xs font-bold text-gray-400 uppercase tracking-wide px-4 py-4 hidden sm:table-cell">Category</th>
                    <th class="text-left text-xs font-bold text-gray-400 uppercase tracking-wide px-4 py-4">Price</th>
                    <th class="text-left text-xs font-bold text-gray-400 uppercase tracking-wide px-4 py-4 hidden md:table-cell">Stock</th>
                    <th class="text-left text-xs font-bold text-gray-400 uppercase tracking-wide px-4 py-4 hidden md:table-cell">Views</th>
                    <th class="text-left text-xs font-bold text-gray-400 uppercase tracking-wide px-4 py-4">Status</th>
                    <th class="text-right text-xs font-bold text-gray-400 uppercase tracking-wide px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
    @endif

                <tr class="hover:bg-gray-50 transition-colors">

                    {{-- Product --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         alt="{{ $product->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate max-w-48">
                                    {{ $product->title }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $product->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Category --}}
                    <td class="px-4 py-4 hidden sm:table-cell">
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                            {{ $product->category->name }}
                        </span>
                    </td>

                    {{-- Price --}}
                    <td class="px-4 py-4">
                        <span class="text-sm font-bold" style="color:var(--orange)">
                            ₱{{ number_format($product->price, 2) }}
                        </span>
                    </td>

                    {{-- Stock --}}
                    <td class="px-4 py-4 hidden md:table-cell">
                        <span class="text-sm font-medium {{ $product->stock <= 3 ? 'text-red-500' : 'text-gray-600' }}">
                            {{ $product->stock }}
                        </span>
                    </td>

                    {{-- Views --}}
                    <td class="px-4 py-4 hidden md:table-cell">
                        <div class="flex items-center gap-1 text-gray-400 text-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ number_format($product->views) }}
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-4">
                        @if($product->status === 'active')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full border border-green-200">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Active
                            </span>
                        @elseif($product->status === 'sold')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-200">
                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                Sold
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full border border-gray-200">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                Inactive
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/products/{{ $product->slug }}"
                               class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all"
                               title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="/seller/products/{{ $product->id }}/edit"
                               class="p-2 text-gray-400 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-all"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="/seller/products/{{ $product->id }}" method="POST"
                                  onsubmit="return confirm('Delete this listing?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                                        title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

    @if($loop->last)
            </tbody>
        </table>
    </div>
    @endif

@empty
    {{-- Empty State --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
             style="background:var(--orange-light)">
            <svg class="w-8 h-8" style="color:var(--orange)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <h3 class="text-gray-700 font-bold text-lg mb-1">No listings yet</h3>
        <p class="text-gray-400 text-sm mb-6">Start selling by creating your first listing</p>
        <a href="/seller/products/create"
           class="btn-orange inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create First Listing
        </a>
    </div>
@endforelse

{{-- Pagination --}}
@if($products->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $products->links() }}
    </div>
@endif

@endsection