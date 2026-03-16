<?php

namespace App\Http\Controllers;

// ============================================================
// USE STATEMENTS
// ============================================================

use App\Models\Order;
// ↑ PARA SA: Kumuha ng orders ng seller — para sa dashboard stats
// GALING SA: app/Models/Order.php

use App\Models\Product;
// ↑ PARA SA: Kumuha ng products ng seller — para sa listing count
// GALING SA: app/Models/Product.php

class SellerController extends Controller
{

    // ============================================================
    // 1. dashboard() — Seller Dashboard
    // ============================================================
    // ROUTE: GET /seller/dashboard
    // SINO: Seller lang — may EnsureSeller middleware
    // LAYUNIN: Ipakita ang seller dashboard na may stats at overview
    // ============================================================

    public function dashboard()
    {
        // ↑ Walang parameter — GET request lang
        //   Lahat ng data ay galing sa database gamit ang auth()->id()

        $sellerId = auth()->id();
        // ↑ I-store ang seller ID sa variable para hindi paulit-ulit
        //   auth()->id() → ID ng currently logged-in seller
        //   GALING SA: Built-in Laravel auth() helper

        // ─────────────────────────────────────────────────
        // STAT 1: Total Sales Amount
        // ─────────────────────────────────────────────────
        $totalSales = Order::where('seller_id', $sellerId)
        // ↑ Lahat ng orders ng seller na ito

                           ->where('status', 'completed')
        // ↑ Completed orders lang — ang cancelled at pending ay hindi counted sa sales
        //   GALING SA: Migration → enum('status', ['pending','confirmed','shipped','completed','cancelled'])

                           ->sum('total_amount');
        // ↑ sum() → SUM(total_amount) — kabuuang halaga ng lahat ng completed orders
        //   GALING SA: Built-in Eloquent aggregate method
        //   RESULT: Halimbawa → ₱15,500.00

        // ─────────────────────────────────────────────────
        // STAT 2: Active Listings Count
        // ─────────────────────────────────────────────────
        $activeListings = Product::where('seller_id', $sellerId)
        // ↑ Products ng seller na ito lang

                                 ->where('status', 'active')
        // ↑ Active lang — hindi sold o inactive

                                 ->count();
        // ↑ count() → COUNT(*) — ilang active products
        //   GALING SA: Built-in Eloquent aggregate method
        //   RESULT: Halimbawa → 8

        // ─────────────────────────────────────────────────
        // STAT 3: Pending Orders Count
        // ─────────────────────────────────────────────────
        $pendingOrders = Order::where('seller_id', $sellerId)
                              ->where('status', 'pending')
        // ↑ Pending lang — mga order na kailangan pa ng aksyon ng seller
        //   Ito ang nagpapakita ng "may kailangan kang gawin" sa dashboard

                              ->count();

        // ─────────────────────────────────────────────────
        // STAT 4: Total Views ng Lahat ng Products
        // ─────────────────────────────────────────────────
        $totalViews = Product::where('seller_id', $sellerId)
                             ->sum('views');
        // ↑ Kabuuang views ng lahat ng products ng seller
        //   GALING SA: products table — views column
        //              (ini-increment sa ProductController@show)

        // ─────────────────────────────────────────────────
        // STAT 5: Recent Orders (pinakabagong 5)
        // ─────────────────────────────────────────────────
        $recentOrders = Order::where('seller_id', $sellerId)
                             ->with('buyer', 'items.product')
        // ↑ 'buyer'         → sino ang bumili (User model)
        //   'items.product' → mga products sa loob ng order

                             ->latest()
                             ->limit(5)
        // ↑ limit(5) → 5 lang ang kukunin — para sa "Recent Orders" section
        //   Hindi kailangan ng lahat — overview lang ito

                             ->get();
        // ↑ get() (hindi paginate) → kailangan ng exact 5 lang
        //   GALING SA: Built-in Eloquent method

        // ─────────────────────────────────────────────────
        // STAT 6: Top 5 Products by Views
        // ─────────────────────────────────────────────────
        $topProducts = Product::where('seller_id', $sellerId)
                              ->orderBy('views', 'desc')
        // ↑ I-sort by views descending — pinaka-maraming views muna
        //   PARA SA: "Ito ang sikat mong products" section sa dashboard

                              ->limit(5)
                              ->get();

        // ─────────────────────────────────────────────────
        // STAT 7: Monthly Sales (para sa Chart.js graph)
        // ─────────────────────────────────────────────────
        $monthlySales = Order::where('seller_id', $sellerId)
                             ->where('status', 'completed')
                             ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
        // ↑ selectRaw() → custom SQL select
        //   MONTH(created_at) → kunin ang buwan ng order
        //   SUM(total_amount) → kabuuang sales per buwan
        //   GALING SA: Built-in Eloquent + raw SQL expression

                             ->whereYear('created_at', date('Y'))
        // ↑ Ngayong taon lang — para sa line chart ng current year

                             ->groupBy('month')
        // ↑ GROUP BY month → isa lang ang entry per buwan

                             ->orderBy('month')
                             ->get();
        // ↑ Resulta: [{'month': 1, 'total': 5000}, {'month': 2, 'total': 8000}, ...]
        //   Gagamitin sa Chart.js para gumawa ng line chart

        // ─────────────────────────────────────────────────
        // IPASA LAHAT SA VIEW
        // ─────────────────────────────────────────────────
        return view('seller.dashboard', compact(
            'totalSales',
            'activeListings',
            'pendingOrders',
            'totalViews',
            'recentOrders',
            'topProducts',
            'monthlySales'
        ));
        // ↑ resources/views/seller/dashboard.blade.php
        //   Ipasa ang lahat ng stats — gagamitin sa view para sa:
        //   → Stat cards (totalSales, activeListings, pendingOrders, totalViews)
        //   → Recent orders table (recentOrders)
        //   → Top products table (topProducts)
        //   → Chart.js line graph (monthlySales)
    }
}