<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Products\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->cartItemsWithTotals();
        return view('shop.cart', compact('cart'));
    }

    public function store($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return redirect()->route('shop')->with('error', 'Product not found');
        }

        $cart = Session::get('cart', []);

        if (array_key_exists($product->id, $cart)) {
            // Increment quantity if product is already in the cart
            $cart[$product->id]['quantity']++;
        } else {
            // Initialize price with regular price
            $price = $product->selling_price;

            // Check if discount price is available and valid
            if ($product->discount_price > 0 && $product->discount_price < $product->selling_price) {
                $price = $product->discount_price;
            }

            // Add the product to the cart
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'buying_price' => $product->buying_price,
                'selling_price' => $price,
                'discount_price' => $product->discount_price,
                'quantity' => 1,
            ];
        }

        Session::put('cart', $cart);

        // Call the method to update cart count
        $this->update_cart_count();

        // Prepare Meta Pixel tracking data
        $pixel_data = [
            'content_name' => $product->title,
            'content_ids' => [$product->id],
            'content_type' => 'product',
            // Using the already calculated price
            // 'value' => $price,
            'currency' => 'KES',
            'contents' => [[
                'id' => $product->id,
                'quantity' => $cart[$product->id]['quantity']
            ]]
        ];

        // Add category if available
        if ($product->product_category) {
            $pixel_data['content_category'] = $product->product_category->title;
        }

        // For AJAX requests
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cart_count' => Session::get('cart_count'),
                // 'pixel_data' => $pixel_data
            ]);
        }

        // For regular requests, store pixel data in session flash
        Session::flash('pixel_data', $pixel_data);
        return redirect()->back()->with('success', 'Product has been added to cart');
    }

    public function update(Request $request, $product_id)
    {
        $request->validate([
            'quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $cart = Session::get('cart', []);

        foreach ($cart as &$product) {
            if ($product['id'] == $product_id) {
                // Update quantity of the matching product
                $product['quantity'] = $request->input('quantity');
                // Update the total price of the product
                $product['total'] = $product['selling_price'] * $product['quantity'];
                break;
            }
        }

        Session::put('cart', $cart);
        $this->update_cart_count();

        return redirect()->route('cart.index')->with('success', 'Quantity has been updated');
    }

    public function destroy($product_id)
    {
        $cart = Session::get('cart', []);

        // Check if the product is in the cart
        if (array_key_exists($product_id, $cart)) {
            // Remove the product from the cart
            unset($cart[$product_id]);

            // Update the session with the modified cart
            Session::put('cart', $cart);

            // Recalculate cart count
            $this->update_cart_count();

            return redirect()->route('cart.index')->with('success', 'Product has been removed from the cart');
        }

        return redirect()->route('cart.index')->with('error', 'Product not found in the cart');
    }

    public function cartItemsWithTotals()
    {
        /*
        *
        * this function returns an array structure like:

        [
            'items' => [
                $product_id_1 => [
                    'id' => 1,
                    'title' => product_title,
                    'slug' => product-title,
                    'quantity' => 3,
                    'price' => 150
                    'total' => 450,
                ],
                $product_id_2 => [
                    'id' => 2,
                    'title' => product_title,
                    'slug' => product-title,
                    'quantity' => 3,
                    'price' => 100
                    'total' => 300,
                ],
            ],
            'subtotal' => 750
        ]

        *
        */

        // Initialize an empty cart and the subtotal to zero
        $cart = ['items' => []];
        $subtotal = 0;

        /*
        * Loop through each item in cart that's stored in the session.
        * Calculate the total price of the item
        * Update the subtotal with the total price of the current item
        * Add the item to the cart
        * Add the calculated subtotal to the cart
        * Return the updated cart with calculated totals
        */
        foreach (Session::get('cart', []) as $product_id => $item) {
            // Use discount price if available, otherwise use regular price
            $price = $item['selling_price'];

            if ($item['discount_price'] !== null && $item['discount_price'] > 0) {
                $price = $item['discount_price'];
            }

            $item['total'] = $price * $item['quantity'];
            $subtotal += $item['total'];
            $cart['items'][$product_id] = $item;
        }

        $cart['subtotal'] = $subtotal;

        return $cart;
    }

    public function update_cart_count()
    {
        $cart = Session::get('cart', []);
        $cart_count = 0;

        foreach ($cart as $item) {
            $cart_count += $item['quantity'];
        }

        Session::put('cart_count', $cart_count);
    }
}
