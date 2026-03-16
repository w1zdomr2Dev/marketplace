@extends('layouts.app')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-gray-900">My Orders</h1>
    <p class="text-gray-400 text-sm mt-0.5">Track your purchases</p>
</div>

{{-- Orders --}}
@forelse($orders as $order)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">

        {{-- Order Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
            <div class="flex items-center gap-4">
                <div>
                    <p class="text-xs text-gray-400">Order</p>
                    <p class="text-sm font-bold text-gray-800">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div>
                    <p class="text-xs text-gray-400">Seller</p>
                    <p class="text-sm font-semibold text-gray-700">{{ $order->seller->name }}</p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div>
                    <p class="text-xs text-gray-400">Date</p>
                    <p class="text-sm font-medium text-gray-600">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            {{-- Status Badge --}}
            @php
                $statusColors = [
                    'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                    'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'shipped'   => 'bg-purple-50 text-purple-700 border-purple-200',
                    'completed' => 'bg-green-50 text-green-700 border-green-200',
                    'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                ];
                $statusDots = [
                    'pending'   => 'bg-yellow-400',
                    'confirmed' => 'bg-blue-400',
                    'shipped'   => 'bg-purple-400',
                    'completed' => 'bg-green-400',
                    'cancelled' => 'bg-red-400',
                ];
            @endphp
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border {{ $statusColors[$order->status] }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $statusDots[$order->status] }}"></span>
                {{ ucfirst($order->status) }}
            </span>
        </div>

        {{-- Order Items --}}
        <div class="px-6 py-4">
            @foreach($order->items as $item)
                <div class="flex items-center gap-4 {{ !$loop->last ? 'mb-3 pb-3 border-b border-gray-50' : '' }}">
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                        @if($item->product && $item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                 alt="{{ $item->product->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">
                            {{ $item->product->title ?? 'Product deleted' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            ₱{{ number_format($item->price_at_purchase, 2) }} × {{ $item->quantity }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-800">
                            ₱{{ number_format($item->subtotal(), 2) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Order Footer --}}
        <div class="px-6 py-4 bg-gray-50 flex items-center justify-between border-t border-gray-100">
            <div>
                <span class="text-xs text-gray-400">Total</span>
                <span class="text-lg font-extrabold ml-2" style="color:var(--orange)">
                    ₱{{ number_format($order->total_amount, 2) }}
                </span>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                {{-- Review Button (completed + no review) --}}
                @if($order->canBeReviewed())
                    <button onclick="document.getElementById('review-{{ $order->id }}').classList.toggle('hidden')"
                            class="text-xs font-semibold px-4 py-2 rounded-xl border"
                            style="border-color:var(--orange-border);color:var(--orange);background:var(--orange-light)">
                        Rate Seller
                    </button>
                @endif

                {{-- Already Reviewed --}}
                @if($order->review)
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $order->review->rating ? 'text-yellow-400' : 'text-gray-200' }}"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="text-xs text-gray-400 ml-1">Reviewed</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Review Form (hidden by default) --}}
        @if($order->canBeReviewed())
            <div id="review-{{ $order->id }}" class="hidden border-t border-gray-100">
                <form action="/reviews" method="POST" class="px-6 py-5">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <h3 class="text-sm font-bold text-gray-700 mb-4">
                        Rate your experience with {{ $order->seller->name }}
                    </h3>

                    {{-- Star Rating --}}
                    <div class="flex items-center gap-1 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="rating" value="{{ $i }}" class="hidden">
                                <svg class="w-8 h-8 text-gray-200 hover:text-yellow-400 transition-colors star-icon"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </label>
                        @endfor
                    </div>

                    <textarea name="comment"
                              rows="2"
                              placeholder="Share your experience (optional)..."
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-transparent resize-none mb-3"
                              style="--tw-ring-color: #F97316"></textarea>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="btn-orange px-6 py-2 rounded-xl font-bold text-sm">
                            Submit Review
                        </button>
                        <button type="button"
                                onclick="document.getElementById('review-{{ $order->id }}').classList.add('hidden')"
                                class="px-4 py-2 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-100 transition-colors border border-gray-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

@empty
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
             style="background:var(--orange-light)">
            <svg class="w-8 h-8" style="color:var(--orange)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 7H4l1-7z"/>
            </svg>
        </div>
        <h3 class="text-gray-700 font-bold text-lg mb-1">No orders yet</h3>
        <p class="text-gray-400 text-sm mb-6">Browse products and place your first order!</p>
        <a href="/" class="btn-orange inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm">
            Browse Products
        </a>
    </div>
@endforelse

{{-- Pagination --}}
@if($orders->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $orders->links() }}
    </div>
@endif

@endsection