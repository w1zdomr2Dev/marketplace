<?php

namespace App\Http\Controllers;

// ============================================================
// USE STATEMENTS
// ============================================================

use App\Models\Review;
// ↑ PARA SA: Review::create() — para i-save ang bagong review
// GALING SA: app/Models/Review.php

use App\Models\Order;
// ↑ PARA SA: Order::find() — para i-check kung completed ang order
// GALING SA: app/Models/Order.php

use App\Models\ActivityLog;
// ↑ PARA SA: ActivityLog::record() — para mag-log ng review
// GALING SA: app/Models/ActivityLog.php

use Illuminate\Http\Request;
// ↑ PARA SA: Ma-access ang form data (rating, comment)
// GALING SA: Built-in Laravel class

class ReviewController extends Controller
{

    // ============================================================
    // 1. store() — Submit a Review (Buyer)
    // ============================================================
    // ROUTE: POST /reviews
    // SINO: Buyer lang — may EnsureBuyer middleware
    // LAYUNIN: I-validate at i-save ang review ng buyer para sa seller
    //
    // RULES:
    // → Completed orders lang ang pwedeng ma-review
    // → Isa lang ang review per order — hindi pwedeng dalawa!
    // → Ang buyer ng order lang ang pwedeng mag-review
    // ============================================================

    public function store(Request $request)
    // ↑ Request $request → form data mula sa review form
    //   Kasama: order_id, rating, comment
    {
        // STEP 1: I-validate ang form data
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            // ↑ required           = hindi pwedeng blangko
            //   exists:orders,id   = kailangan valid ang order ID
            //                       Para hindi pwedeng mag-fake ng order ID

            'rating'   => 'required|integer|min:1|max:5',
            // ↑ integer  = whole number lang — hindi pwedeng 3.5
            //   min:1    = minimum na 1 star
            //   max:5    = maximum na 5 stars
            //   GALING SA: Migration → $table->tinyInteger('rating')

            'comment'  => 'nullable|string|max:500',
            // ↑ nullable   = optional — pwedeng walang comment
            //   max:500    = maximum 500 characters lang
        ]);

        // STEP 2: Hanapin ang order
        $order = Order::where('id', $request->order_id)
                      ->where('buyer_id', auth()->id())
        // ↑ OWNERSHIP CHECK — kailangan ikaw ang buyer ng order na ito!
        //   Hindi pwedeng mag-review ng order ng ibang buyer!
        //   SECURITY: Laging i-verify ang ownership

                      ->firstOrFail();
        // ↑ 404 kung hindi makita o hindi mo order

        // STEP 3: I-check kung COMPLETED ang order
        if ($order->status !== 'completed') {
            return back()->with('error', 'You can only review completed orders.');
            // ↑ BUSINESS RULE: Hindi pwedeng mag-review ng order na hindi pa tapos!
            //   Pending, confirmed, shipped — hindi pa pwede
            //   Completed lang — siguradong natanggap na ng buyer ang product
        }

        // STEP 4: I-check kung wala pang review ang order na ito
        if ($order->review) {
        // ↑ $order->review → checks the hasOne Review relationship
        //   GALING SA: Order Model:
        //              public function review() { return $this->hasOne(Review::class) }
        //   Kung may laman → may review na!

            return back()->with('error', 'You have already reviewed this order.');
            // ↑ BUSINESS RULE: Isa lang ang review per order!
            //   Para hindi pwedeng bigyan ng maraming beses ng rating ang seller
            //   sa iisang transaksyon
        }

        // STEP 5: I-save ang review
        Review::create([
        // ↑ INSERT INTO reviews ...
        //   GALING SA: Eloquent ORM

            'order_id'    => $order->id,
            // ↑ I-link sa order — para malaman kung anong order ang na-review

            'reviewer_id' => auth()->id(),
            // ↑ Sino ang nag-review — ang logged-in buyer
            //   HINDI galing sa form — para secure!

            'seller_id'   => $order->seller_id,
            // ↑ Sino ang na-review — ang seller ng order
            //   GALING SA: orders table — seller_id column

            'rating'      => $request->rating,
            // ↑ Ilang stars ang ibinigay — 1 to 5
            //   GALING SA: Form data

            'comment'     => $request->comment,
            // ↑ Ang comment ng buyer — optional
            //   GALING SA: Form data
        ]);

        // STEP 6: I-log ang action
        ActivityLog::record(
            'submitted_review',
            'Order',
            $order->id,
            [
                'rating'  => $request->rating,
                'seller'  => $order->seller_id,
            ]
        );

        // STEP 7: I-redirect na may success message
        return redirect('/orders')
               ->with('success', 'Review submitted successfully! Thank you for your feedback.');
    }
}