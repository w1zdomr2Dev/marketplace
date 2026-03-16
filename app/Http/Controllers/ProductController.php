<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ActivityLog;  // ← DAGDAG — kulang sa original
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('status', 'active')
                           ->with('seller', 'category')
                           ->latest()
                           ->paginate(12);

        return view('products.index', compact('products'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
                          ->with('seller', 'category')
                          ->firstOrFail();

        $product->incrementViews();

        return view('products.show', compact('product'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $products = Product::where('status', 'active')
                           ->where('title', 'like', "%{$query}%")
                           // ✅ FIX: double quotes → para mag-work ang variable sa loob
                           // ❌ DATI: '%{$query}%' → single quotes → hindi nag-eexpand ang variable
                           // ✅ TAMA: "%{$query}%" → double quotes → nag-eexpand ang $query

                           ->with('seller', 'category')
                           ->latest()
                           ->paginate(12);
                           // ✅ FIX: ->paginate() → may dot (.) bago
                           // ❌ DATI: -paginate() → kulang ang dot

        return view('products.index', compact('products', 'query'));
    }

    public function sellerIndex()
    {
        $products = Product::where('seller_id', auth()->id())
                           ->latest()
                           ->paginate(10);
                           // ✅ FIX: ->paginate() → tama ang spelling
                           // ❌ DATI: ->pagination() → mali ang spelling — walang ganyang method!

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('seller.products.create', compact('categories'));
        // ✅ FIX: compact('categories') → ipasa ang $categories sa view
        // ❌ DATI: walang compact — hindi maaaccess ang $categories sa blade!
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:1',
            'stock'       => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            // ✅ FIX: exists:categories,id → comma at plural
            // ❌ DATI: exist:categories.id → typo (exist vs exists) + dot vs comma

            'location'    => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                                          ->store('products', 'public');
        }

        Product::create([
            ...$validated,
            'seller_id' => auth()->id(),
            // ✅ FIX: auth()->id() → walang quotes — ito ay PHP function call!
            // ❌ DATI: 'auth()->id()' → naka-string — literally na "auth()->id()" ang nise-save!

            'slug' => Str::slug($request->title) . '-' . time(),
            // ✅ FIX: => at walang quotes sa value
            // ❌ DATI: 'slug' = ... → mali ang assignment operator sa array
            //                         dapat => hindi =
        ]);

        ActivityLog::record(
            'listed_product',
            'Product',
            null,
            ['title' => $request->title]
            // ✅ FIX: actual PHP array → ['title' => $request->title]
            // ❌ DATI: naka-string na '[title => $request->title]'
            //          = literal string, hindi actual array!
        );

        return redirect('/seller/products')
               ->with('success', 'Product listed successfully!');
               // ✅ FIX: -> may dalawang dashes at greater than
               // ❌ DATI: >with() → kulang ang dash (-)
    }

    public function edit($id)
    {
        $product = Product::where('id', $id)
        // ✅ FIX: $product → singular (isa lang ang hinahanap)
        // ❌ DATI: $products → plural — nakakalito + inconsistent sa ibaba

                          ->where('seller_id', auth()->id())
                          // ✅ FIX: comma → , auth()->id()
                          // ❌ DATI: =>  → mali ang syntax sa where() — dapat comma

                          ->firstOrFail();

        $categories = Category::all();
        // ✅ FIX: may semicolon sa dulo
        // ❌ DATI: walang semicolon — syntax error!

        return view('seller.products.edit', compact('product', 'categories'));
        // ✅ FIX: 'product' → singular, may semicolon
        // ❌ DATI: 'products' → plural + walang semicolon
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('id', $id)
                          ->where('seller_id', auth()->id())
                          ->firstOrFail();
                          // ✅ FIX: ->firstOrFail() → may dot (.) bago
                          // ❌ DATI: -firstOrFail() → kulang ang dot

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:1',
            'stock'       => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'location'    => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
            // ✅ FIX: $validated['image'] = → i-update ang image key ng validated array
            // ❌ DATI: $validated = → pinalitan ang BUONG validated array!
            //          Ibig sabihin — nawala ang title, price, etc.!

                                          ->store('products', 'public');
                                          // ✅ FIX: 'products' → plural
                                          // ❌ DATI: 'product' → singular — mali ang folder name
        }

        $product->update($validated);
        // ✅ FIX: $validated → actual PHP variable
        // ❌ DATI: 'validated' → naka-string — hindi ito variable!
        //          Nag-pass ng literal string "validated" hindi ang array

        return redirect('/seller/products')
               ->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::where('id', $id)
                          ->where('seller_id', auth()->id())
                          ->firstOrFail();

        ActivityLog::record(
            'deleted_product',
            'Product',
            $id,
            ['product_id' => $id]
        );

        $product->delete();

        return redirect('/seller/products')
               ->with('success', 'Product deleted successfully!');
    }
}