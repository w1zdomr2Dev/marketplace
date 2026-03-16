<?php

namespace App\Http\Controllers;

// ============================================================
// USE STATEMENTS
// ============================================================

use App\Models\Product;
// ↑ PARA SA: Product::findOrFail() — hanapin ang product
// GALING SA: app/Models/Product.php

use Illuminate\Http\Request;
// ↑ PARA SA: Ma-access ang request data
// GALING SA: Built-in Laravel class

class FavoriteController extends Controller
{

    // ============================================================
    // 1. toggle() — Add or Remove Favorite (Buyer)
    // ============================================================
    // ROUTE: POST /favorites/{product}
    // SINO: Buyer lang — may EnsureBuyer middleware
    // LAYUNIN: I-add o i-remove ang product sa favorites ng buyer
    //
    // TOGGLE = isang method para sa dalawang action:
    //   → Kung WALA pa sa favorites → I-ADD (attach)
    //   → Kung NANDOON na           → I-REMOVE (detach)
    // ============================================================

    public function toggle($productId)
    // ↑ $productId → galing sa URL — /favorites/{product}
    //   EXAMPLE: /favorites/5 → $productId = 5
    {
        // STEP 1: Hanapin ang product
        $product = Product::findOrFail($productId);
        // ↑ findOrFail() → hanapin ang product gamit ang ID
        //   Kung hindi makita → automatic 404
        //   GALING SA: Built-in Eloquent method

        // STEP 2: Kunin ang logged-in buyer
        $user = auth()->user();
        // ↑ auth()->user() → ang currently logged-in User object
        //   GALING SA: Built-in Laravel auth() helper
        //   DIFFERENCE sa auth()->id():
        //     auth()->id()   → number lang (3)
        //     auth()->user() → buong User object (para magamit ang relationships)

        // STEP 3: I-toggle ang favorite
        $user->favoriteProducts()->toggle($productId);
        // ↑ favoriteProducts() → relationship sa User Model
        //   GALING SA: User Model:
        //              public function favoriteProducts() {
        //                  return $this->belongsToMany(Product::class, 'favorites');
        //              }
        //
        // ↑ toggle() → built-in Laravel belongsToMany method
        //   KUNG WALA PA SA favorites TABLE:
        //     → INSERT INTO favorites (user_id, product_id) VALUES (?, ?)
        //     → Nag-ADD ng favorite
        //   KUNG NANDOON NA SA favorites TABLE:
        //     → DELETE FROM favorites WHERE user_id = ? AND product_id = ?
        //     → Nag-REMOVE ng favorite
        //
        //   GALING SA: Built-in Laravel pivot table method
        //   BAKIT toggle() at hindi attach() at detach() ng hiwalay:
        //     → Mas malinis — isa lang ang method
        //     → Laravel na ang bahala kung add o remove

        // STEP 4: I-check kung naka-add o naka-remove
        $isFavorited = $user->favoriteProducts()
                            ->where('product_id', $productId)
                            ->exists();
        // ↑ exists() → check kung nandoon pa rin sa favorites
        //   TRUE  = nag-add tayo (hindi pa nandoon dati)
        //   FALSE = nag-remove tayo (nandoon na dati)
        //   GALING SA: Built-in Eloquent method

        // STEP 5: I-redirect na may success message
        $message = $isFavorited
            ? 'Product added to favorites!'
            : 'Product removed from favorites!';
        // ↑ Ternary operator — short way ng if/else:
        //   Kung $isFavorited = true  → "added to favorites"
        //   Kung $isFavorited = false → "removed from favorites"

        return back()->with('success', $message);
        // ↑ back() → i-redirect pabalik sa dating page
        //   Kasi ang toggle button ay nasa product page —
        //   pagkatapos ng toggle, manatili sa product page
    }
}