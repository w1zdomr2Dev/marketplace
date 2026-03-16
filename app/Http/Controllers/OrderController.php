<?php

namespace App\Http\Controllers;

// ============================================================
// USE STATEMENTS
// ============================================================

use App\Models\Order;
// ↑ PARA SA: Order::create(), Order::where(), $order->update()
// GALING SA: app/Models/Order.php

use App\Models\OrderItem;
// ↑ PARA SA: OrderItem::create() — para sa items ng bawat order
// GALING SA: app/Models/OrderItem.php

use App\Models\Product;
// ↑ PARA SA: Product::find() — para i-check kung available ang product
// GALING SA: app/Models/Product.php

use App\Models\ActivityLog;
// ↑ PARA SA: ActivityLog::record() — para mag-log ng actions
// GALING SA: app/Models/ActivityLog.php

use Illuminate\Http\Request;
// ↑ PARA SA: Lahat ng POST/PATCH requests — para ma-access ang form data
// GALING SA: Built-in Laravel class

class OrderController extends Controller
{

    // ============================================================
    // 1. index() — View My Orders (Buyer)
    // ============================================================
    // ROUTE: GET /orders
    // SINO: Buyer lang — may EnsureBuyer middleware
    // LAYUNIN: Ipakita ang lahat ng orders ng logged-in buyer
    // ============================================================

    public function index()
    {
        $orders = Order::where('buyer_id', auth()->id())
        // ↑ auth()->id() → ID ng currently logged-in buyer
        //   BAKIT: Hindi natin gustong makita ng buyer ang orders ng iba!
        //          Sarili lang niya ang makikita.
        //   GALING SA: Built-in Laravel auth() helper

                       ->with('seller', 'items.product')
        // ↑ PARA SA: I-load ang seller at items ng bawat order
        //   'seller'       → sino ang nagbenta (User model)
        //   'items'        → mga products sa loob ng order (OrderItem model)
        //   'items.product' → para sa bawat item, i-load din ang product info
        //   BAKIT: Sa orders page kailangan nating ipakita:
        //          "Binili mo kay Maria Santos"
        //          "Binili mo: Nike Shoes x1, Bag x2"
        //   GALING SA: Order Model relationships:
        //              public function seller() { return $this->belongsTo(User::class) }
        //              public function items() { return $this->hasMany(OrderItem::class) }

                       ->latest()
        // ↑ Pinakabago muna — newest orders sa taas
        // GALING SA: Built-in Eloquent method

                       ->paginate(10);
        // ↑ 10 orders per page lang

        return view('orders.index', compact('orders'));
        // ↑ resources/views/orders/index.blade.php
        //   Ipasa ang $orders — makikita ng buyer ang kanyang order history
    }


    // ============================================================
    // 2. store() — Place a New Order (Buyer)
    // ============================================================
    // ROUTE: POST /orders
    // SINO: Buyer lang — may EnsureBuyer middleware
    // LAYUNIN: I-validate at i-save ang bagong order
    // ============================================================

    public function store(Request $request)
    // ↑ Request $request → form data mula sa "Place Order" button
    {
        // STEP 1: I-validate ang form data
        $request->validate([
            'product_id' => 'required|exists:products,id',
            // ↑ required        = hindi pwedeng blangko
            //   exists:products,id = kailangan valid ang product ID
            //                     Nagche-check sa products table
            //                     Para hindi pwedeng mag-fake ng product ID

            'quantity'   => 'required|integer|min:1',
            // ↑ integer = whole number lang
            //   min:1   = hindi pwedeng 0 o negative
        ]);

        // STEP 2: Hanapin ang product
        $product = Product::findOrFail($request->product_id);
        // ↑ findOrFail() → hanapin ang product gamit ang ID
        //   Kung hindi makita → automatic 404
        //   GALING SA: Built-in Eloquent method

        // STEP 3: I-check kung available pa ang product
        if (!$product->isAvailable()) {
        // ↑ isAvailable() → helper method sa Product Model
        //   Returns true kung: status = 'active' AT stock > 0
        //   GALING SA: Product Model:
        //              public function isAvailable(): bool {
        //                  return $this->status === 'active' && $this->stock > 0;
        //              }

            return back()->with('error', 'Sorry, this product is no longer available.');
            // ↑ back() → i-redirect pabalik sa nakaraang page
            //   with('error', '...') → flash error message
        }

        // STEP 4: I-check kung hindi siya ang seller ng product
        if ($product->seller_id === auth()->id()) {
            return back()->with('error', 'You cannot order your own product.');
            // ↑ Hindi pwedeng mag-order ang seller ng sarili niyang product!
            //   SECURITY: Mahalaga ito para hindi ma-game ang system
        }

        // STEP 5: Gumawa ng bagong Order record
        $order = Order::create([
        // ↑ INSERT INTO orders ...
        //   GALING SA: Eloquent ORM

            'buyer_id'     => auth()->id(),
            // ↑ Sino ang bumili — ang logged-in buyer
            //   HINDI galing sa form — para secure!

            'seller_id'    => $product->seller_id,
            // ↑ Sino ang nagbenta — galing sa product record
            //   GALING SA: products table — seller_id column

            'total_amount' => $product->price * $request->quantity,
            // ↑ Kabuuang bayad = presyo × dami
            //   GALING SA: products table — price column

            'status'       => 'pending',
            // ↑ Default status — pending muna hanggang hindi pa nag-confirm ang seller
            //   GALING SA: Migration → enum('status', ['pending','confirmed',...])
        ]);

        // STEP 6: Gumawa ng OrderItem record
        OrderItem::create([
        // ↑ Ang laman ng order — separate table para sa multiple items support

            'order_id'          => $order->id,
            // ↑ I-link sa order na bagong ginawa

            'product_id'        => $product->id,
            // ↑ Anong product ang binili

            'quantity'          => $request->quantity,
            // ↑ Ilang piraso ang binili

            'price_at_purchase' => $product->price,
            // ↑ MAHALAGA: I-store ang KASALUKUYANG presyo!
            //   BAKIT: Kapag nagbago ang presyo ng product mamaya —
            //          hindi magbabago ang history ng order mo!
            //          Permanenteng naka-record ang presyo nung binili mo.
        ]);

        // STEP 7: Bawasan ang stock ng product
        $product->decrement('stock', $request->quantity);
        // ↑ decrement() → bawasan ang stock ng biniling quantity
        //   EXAMPLE: stock = 10, binili = 2 → stock = 8
        //   GALING SA: Built-in Eloquent method

        // STEP 8: I-log ang action
        ActivityLog::record(
            'placed_order',
            'Order',
            $order->id,
            [
                'product'  => $product->title,
                'quantity' => $request->quantity,
                'total'    => $order->total_amount,
            ]
        );

        // STEP 9: I-redirect na may success message
        return redirect('/orders')
               ->with('success', 'Order placed successfully! Waiting for seller confirmation.');
    }


    // ============================================================
    // 3. updateStatus() — Update Order Status (Seller)
    // ============================================================
    // ROUTE: PATCH /orders/{id}/status
    // SINO: Seller lang — may EnsureSeller middleware
    // LAYUNIN: I-update ang status ng order
    //          (pending → confirmed → shipped → completed)
    // ============================================================

    public function updateStatus(Request $request, $id)
    // ↑ Request $request → ang bagong status mula sa form
    // ↑ $id → ang ID ng order na ina-update (galing sa URL)
    {
        // STEP 1: Hanapin ang order at i-check kung seller mo ito
        $order = Order::where('id', $id)
                      ->where('seller_id', auth()->id())
        // ↑ OWNERSHIP CHECK — para masiguradong seller mo ang order na ito!
        //   Hindi pwedeng mag-update ng status ng order ng ibang seller!
        //   SECURITY: Laging i-verify ang ownership bago mag-update

                      ->firstOrFail();
        // ↑ 404 kung hindi makita o hindi mo order

        // STEP 2: I-validate ang bagong status
        $request->validate([
            'status' => 'required|in:confirmed,shipped,completed,cancelled',
            // ↑ in:... = isa lang sa mga ito ang pwedeng ilagay
            //   BAKIT: Hindi pwedeng mag-set ng random status tulad ng 'hacked'
            //   GALING SA: Migration → enum('status', [...])
            //   TANDAAN: 'pending' wala dito — hindi na pwedeng bumalik sa pending!
        ]);

        // STEP 3: I-check kung valid ang status transition
        $validTransitions = [
            'pending'   => ['confirmed', 'cancelled'],
            // ↑ Mula pending: pwedeng confirmed o cancelled lang

            'confirmed' => ['shipped', 'cancelled'],
            // ↑ Mula confirmed: pwedeng shipped o cancelled lang

            'shipped'   => ['completed'],
            // ↑ Mula shipped: completed lang — hindi na pwedeng i-cancel!

            'completed' => [],
            // ↑ Mula completed: wala nang pwedeng gawin — tapos na!
        ];

        if (!in_array($request->status, $validTransitions[$order->status])) {
            return back()->with('error', 'Invalid status transition.');
            // ↑ Halimbawa: Hindi pwedeng mula shipped → confirmed
            //             Pababa lang ang flow, hindi pwedeng umakyat
        }

        // STEP 4: I-update ang status
        $order->update(['status' => $request->status]);
        // ↑ UPDATE orders SET status = ? WHERE id = ?
        //   GALING SA: Built-in Eloquent method

        // STEP 5: Kung completed — i-update ang product status kung sold out na
        if ($request->status === 'completed') {
            $order->items->each(function ($item) {
                if ($item->product->stock === 0) {
                    $item->product->update(['status' => 'sold']);
                    // ↑ Kapag wala nang stock → i-mark bilang sold
                }
            });
        }

        // STEP 6: I-log ang action
        ActivityLog::record(
            'updated_order_status',
            'Order',
            $order->id,
            ['new_status' => $request->status]
        );

        return redirect()->back()
               ->with('success', 'Order status updated to ' . $request->status . '!');
    }
}