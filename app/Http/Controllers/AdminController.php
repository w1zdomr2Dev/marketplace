<?php

namespace App\Http\Controllers;

// ============================================================
// USE STATEMENTS
// ============================================================

use App\Models\User;
// ↑ PARA SA: User::all(), User::find() — para sa user management
// GALING SA: app/Models/User.php

use App\Models\Product;
// ↑ PARA SA: Product::find(), Product::delete() — para sa listing management
// GALING SA: app/Models/Product.php

use App\Models\Order;
// ↑ PARA SA: Order stats — para sa platform analytics
// GALING SA: app/Models/Order.php

use App\Models\ActivityLog;
// ↑ PARA SA: Ipakita ang logs + mag-record ng admin actions
// GALING SA: app/Models/ActivityLog.php

use Illuminate\Http\Request;
// ↑ PARA SA: Search/filter parameters sa GET requests
// GALING SA: Built-in Laravel class

class AdminController extends Controller
{

    // ============================================================
    // 1. dashboard() — Admin Dashboard
    // ============================================================
    // ROUTE: GET /admin/dashboard
    // SINO: Admin lang — may EnsureAdmin middleware
    // LAYUNIN: Ipakita ang platform-wide analytics at overview
    // ============================================================

    public function dashboard()
    {
        // ─────────────────────────────────────────────────
        // PLATFORM STATS
        // ─────────────────────────────────────────────────

        $totalUsers = User::count();
        // ↑ Kabuuang bilang ng lahat ng registered users
        //   KASAMA: buyers, sellers, admin
        //   count() → SELECT COUNT(*) FROM users
        //   GALING SA: Built-in Eloquent aggregate method

        $totalBuyers = User::where('role', 'buyer')->count();
        // ↑ Ilang buyers ang naka-register
        //   GALING SA: users table — role column

        $totalSellers = User::where('role', 'seller')->count();
        // ↑ Ilang sellers ang naka-register

        $totalListings = Product::count();
        // ↑ Kabuuang bilang ng lahat ng product listings
        //   KASAMA: active, sold, inactive

        $activeListings = Product::where('status', 'active')->count();
        // ↑ Ilang listings ang currently active

        $totalOrders = Order::count();
        // ↑ Kabuuang bilang ng lahat ng orders

        $totalSalesVolume = Order::where('status', 'completed')
                                 ->sum('total_amount');
        // ↑ Kabuuang sales ng buong platform
        //   COMPLETED orders lang — ang cancelled ay hindi counted
        //   sum('total_amount') → SUM(total_amount)

        $bannedUsers = User::where('is_active', false)->count();
        // ↑ Ilang users ang currently banned
        //   GALING SA: users table — is_active column

        // ─────────────────────────────────────────────────
        // RECENT ACTIVITY
        // ─────────────────────────────────────────────────

        $recentLogs = ActivityLog::with('user')
        // ↑ I-load ang user info ng bawat log
        //   GALING SA: ActivityLog Model:
        //              public function user() { return $this->belongsTo(User::class) }

                                 ->latest()
                                 ->limit(10)
                                 ->get();
        // ↑ Pinakabagong 10 activities sa platform
        //   Para sa "Recent Activity" section ng admin dashboard

        // ─────────────────────────────────────────────────
        // MONTHLY PLATFORM SALES (para sa Chart.js)
        // ─────────────────────────────────────────────────

        $monthlySales = Order::where('status', 'completed')
                             ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
                             ->whereYear('created_at', date('Y'))
                             ->groupBy('month')
                             ->orderBy('month')
                             ->get();
        // ↑ Same logic sa SellerController@dashboard
        //   Pero ito ay LAHAT ng orders — hindi filter ng seller

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalBuyers',
            'totalSellers',
            'totalListings',
            'activeListings',
            'totalOrders',
            'totalSalesVolume',
            'bannedUsers',
            'recentLogs',
            'monthlySales'
        ));
        // ↑ resources/views/admin/dashboard.blade.php
    }


    // ============================================================
    // 2. users() — Manage All Users
    // ============================================================
    // ROUTE: GET /admin/users
    // SINO: Admin lang — may EnsureAdmin middleware
    // LAYUNIN: Ipakita ang lahat ng users na may search at filter
    // ============================================================

    public function users(Request $request)
    // ↑ Request $request → para sa optional search at filter parameters
    {
        $query = User::query();
        // ↑ query() → gumawa ng query builder — pwede pang dagdagan ng conditions
        //   BAKIT hindi agad User::all():
        //   Kasi may optional filters pa — hindi lagi may filter ang user
        //   GALING SA: Built-in Eloquent method

        // OPTIONAL SEARCH — kung may ?search= sa URL
        if ($request->has('search') && $request->search) {
        // ↑ has('search') → check kung may search parameter sa URL
        //   $request->search → ang value ng search parameter

            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                // ↑ Hanapin sa name column

                  ->orWhere('email', 'like', '%' . $request->search . '%');
                // ↑ O sa email column
                //   orWhere() → OR sa SQL — kahit isa sa dalawa ang match
            });
        }

        // OPTIONAL FILTER BY ROLE — kung may ?role= sa URL
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
            // ↑ Filter by role: buyer, seller, o admin
        }

        // OPTIONAL FILTER BY STATUS — kung may ?status= sa URL
        if ($request->has('status')) {
            if ($request->status === 'banned') {
                $query->where('is_active', false);
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            }
        }

        $users = $query->latest()->paginate(20);
        // ↑ I-execute ang query — 20 users per page
        //   latest() → pinakabago muna (newest registered)

        return view('admin.users', compact('users'));
        // ↑ resources/views/admin/users.blade.php
        //   May Ban/Unban button bawat user
    }


    // ============================================================
    // 3. ban() — Ban or Unban a User
    // ============================================================
    // ROUTE: PATCH /admin/users/{id}/ban
    // SINO: Admin lang — may EnsureAdmin middleware
    // LAYUNIN: I-toggle ang is_active ng user (ban o unban)
    // ============================================================

    public function ban($id)
    // ↑ $id → ang ID ng user na iba-ban/unban (galing sa URL)
    {
        // STEP 1: Hanapin ang user
        $user = User::findOrFail($id);
        // ↑ findOrFail() → 404 kung hindi makita ang user
        //   GALING SA: Built-in Eloquent method

        // STEP 2: I-check kung hindi admin ang user na iba-ban
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot ban an admin account.');
            // ↑ SECURITY: Hindi pwedeng mag-ban ng ibang admin!
            //   Para hindi ma-lockout ang buong admin system
            //   GALING SA: User Model helper method: isAdmin()
        }

        // STEP 3: I-check kung hindi siya ang naka-login na admin
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot ban yourself.');
            // ↑ SECURITY: Hindi pwedeng mag-ban ng sarili!
        }

        // STEP 4: I-toggle ang is_active
        $user->update([
            'is_active' => !$user->is_active,
            // ↑ ! = NOT operator — i-flip ang value
            //   Kung TRUE (active) → FALSE (banned)
            //   Kung FALSE (banned) → TRUE (unbanned)
            //   GALING SA: users table — is_active column
        ]);

        // STEP 5: I-log ang action
        $action = $user->is_active ? 'unbanned_user' : 'banned_user';
        // ↑ Depende sa bagong status — anong action ang i-lo-log
        //   TANDAAN: is_active ay UPDATED na rito — kaya baligtad na ang check
        //   Kung is_active = true (after update) → nag-unban tayo
        //   Kung is_active = false (after update) → nag-ban tayo

        ActivityLog::record(
            $action,
            'User',
            $user->id,
            ['target_user' => $user->name]
        );

        // STEP 6: I-redirect na may success message
        $message = $user->is_active
            ? "User {$user->name} has been unbanned."
            : "User {$user->name} has been banned.";

        return back()->with('success', $message);
    }


    // ============================================================
    // 4. removeProduct() — Remove a Listing
    // ============================================================
    // ROUTE: DELETE /admin/products/{id}
    // SINO: Admin lang — may EnsureAdmin middleware
    // LAYUNIN: Burahin ang fake o illegal na listing
    // ============================================================

    public function removeProduct($id)
    // ↑ $id → ang ID ng product na ire-remove (galing sa URL)
    {
        // STEP 1: Hanapin ang product
        $product = Product::findOrFail($id);
        // ↑ findOrFail() → 404 kung hindi makita
        //   WALANG ownership check dito!
        //   Admin ay may kapangyarihang burahin ang KAHIT ANONG listing

        // STEP 2: I-log BAGO burahin — para may record pa
        ActivityLog::record(
            'removed_product',
            'Product',
            $product->id,
            [
                'title'     => $product->title,
                'seller_id' => $product->seller_id,
            ]
        );
        // ↑ BAKIT log BAGO mag-delete at hindi PAGKATAPOS:
        //   Pagkatapos ng delete — wala na ang product record!
        //   Hindi na natin malalaman ang title at seller nito.
        //   Kaya i-log muna BAGO burahin.

        // STEP 3: Burahin ang product
        $product->delete();
        // ↑ DELETE FROM products WHERE id = ?
        //   GALING SA: Built-in Eloquent method

        return back()->with('success', 'Listing removed successfully.');
    }


    // ============================================================
    // 5. logs() — View Activity Logs
    // ============================================================
    // ROUTE: GET /admin/logs
    // SINO: Admin lang — may EnsureAdmin middleware
    // LAYUNIN: Ipakita ang lahat ng activity logs ng platform
    // ============================================================

    public function logs(Request $request)
    // ↑ Request $request → para sa optional filter parameters
    {
        $query = ActivityLog::with('user');
        // ↑ I-load ang user info ng bawat log
        //   Para makita sa view: "Juan dela Cruz did placed_order"
        //   GALING SA: ActivityLog Model relationship

        // OPTIONAL FILTER BY ACTION
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
            // ↑ Filter by specific action
            //   EXAMPLE: ?action=placed_order → ipakita lahat ng orders lang
        }

        // OPTIONAL FILTER BY DATE
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
            // ↑ whereDate() → filter by specific date (Y-m-d format)
            //   EXAMPLE: ?date=2024-01-15 → lahat ng logs sa Jan 15
            //   GALING SA: Built-in Eloquent method
        }

        // OPTIONAL FILTER BY USER
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
            // ↑ Filter by specific user
        }

        $logs = $query->latest()->paginate(20);
        // ↑ I-execute ang query — 20 logs per page
        //   latest() → pinakabago muna — pinaka-recent na activity sa taas

        return view('admin.logs', compact('logs'));
        // ↑ resources/views/admin/logs.blade.php
        //   Makikita ang lahat ng nangyari sa platform:
        //   "Juan → placed_order → Order #12 → ₱500 → 10:30 AM"
    }
}