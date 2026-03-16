 
@extends('layouts.app')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900">
            Welcome back, {{ auth()->user()->name }}! 👋
        </h1>
        <p class="text-gray-400 text-sm mt-0.5">Here's how your store is doing</p>
    </div>
    <a href="/seller/products/create"
       class="btn-orange px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Listing
    </a>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- STAT CARDS --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Sales --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Sales</p>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:var(--orange-light)">
                <svg class="w-5 h-5" style="color:var(--orange)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-gray-900">
            ₱{{ number_format($totalSales, 2) }}
        </p>
        <p class="text-xs text-gray-400 mt-1">Completed orders</p>
    </div>

    {{-- Active Listings --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Listings</p>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-blue-50">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-gray-900">{{ $activeListings }}</p>
        <p class="text-xs text-gray-400 mt-1">Active products</p>
    </div>

    {{-- Pending Orders --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pending</p>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-yellow-50">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-gray-900">{{ $pendingOrders }}</p>
        <p class="text-xs {{ $pendingOrders > 0 ? 'text-yellow-500 font-semibold' : 'text-gray-400' }} mt-1">
            {{ $pendingOrders > 0 ? 'Need your action!' : 'All caught up!' }}
        </p>
    </div>

    {{-- Total Views --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Views</p>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-purple-50">
                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-gray-900">{{ number_format($totalViews) }}</p>
        <p class="text-xs text-gray-400 mt-1">Across all listings</p>
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- RECENT ORDERS + TOP PRODUCTS --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Recent Orders --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-bold text-gray-800">Recent Orders</h2>
            <a href="/seller/products" class="text-xs font-semibold" style="color:var(--orange)">
                View all →
            </a>
        </div>

        @forelse($recentOrders as $order)
            <div class="flex items-center justify-between px-6 py-3.5 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                         style="background:var(--orange)">
                        {{ strtoupper(substr($order->buyer->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $order->buyer->name }}
                        </p>
                        <p class="text-xs text-gray-400">
                            ₱{{ number_format($order->total_amount, 2) }} · {{ $order->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                @php
                    $statusColors = [
                        'pending'   => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'shipped'   => 'bg-purple-50 text-purple-700 border-purple-200',
                        'completed' => 'bg-green-50 text-green-700 border-green-200',
                        'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                    ];
                @endphp
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $statusColors[$order->status] }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        @empty
            <div class="px-6 py-10 text-center">
                <p class="text-gray-400 text-sm">No orders yet</p>
            </div>
        @endforelse
    </div>

    {{-- Top Products --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
            <h2 class="text-sm font-bold text-gray-800">Top Products by Views</h2>
            <a href="/seller/products" class="text-xs font-semibold" style="color:var(--orange)">
                View all →
            </a>
        </div>

        @forelse($topProducts as $product)
            <div class="flex items-center gap-3 px-6 py-3.5 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                {{-- Rank --}}
                <span class="text-xs font-bold text-gray-300 w-5 text-center">
                    {{ $loop->iteration }}
                </span>

                {{-- Image --}}
                <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $product->title }}</p>
                    <p class="text-xs text-gray-400">₱{{ number_format($product->price, 2) }}</p>
                </div>

                {{-- Views --}}
                <div class="flex items-center gap-1 text-gray-400 text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ number_format($product->views) }}
                </div>
            </div>
        @empty
            <div class="px-6 py-10 text-center">
                <p class="text-gray-400 text-sm">No products yet</p>
                <a href="/seller/products/create"
                   class="text-xs font-semibold mt-1 inline-block" style="color:var(--orange)">
                    Create your first listing →
                </a>
            </div>
        @endforelse
    </div>

</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- MONTHLY SALES CHART --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-50">
        <h2 class="text-sm font-bold text-gray-800">Monthly Sales — {{ date('Y') }}</h2>
    </div>
    <div class="p-6">
        <canvas id="salesChart" height="80"></canvas>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    // Prepare data from Laravel → JavaScript
    const monthlyData = @json($monthlySales);

    // Build 12-month labels and data
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const salesData = Array(12).fill(0);

    monthlyData.forEach(item => {
        salesData[item.month - 1] = parseFloat(item.total);
    });

    // Render chart
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Sales (₱)',
                data: salesData,
                borderColor: '#F97316',
                backgroundColor: 'rgba(249, 115, 22, 0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#F97316',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ₱' + ctx.raw.toLocaleString()
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        callback: val => '₱' + val.toLocaleString(),
                        font: { size: 11 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
</script>

@endsection