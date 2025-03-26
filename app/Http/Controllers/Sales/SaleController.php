<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('items', 'user')
            ->latest()
            ->get();
        $count_sales = $sales->count();

        return view('admin.sales.index', compact('count_sales', 'sales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,mpesa,card',
        ]);

        return DB::transaction(function () use ($request) {
            $orderNumber = 'POS-' . Carbon::now()->format('dmY') . '-' . Str::upper(Str::random(4));

            // Create a new sale
            $sale = Sale::create([
                'order_number' => $orderNumber,
                'order_type' => true,  // Can be modified if needed
                'discount_code' => $request->discount_code ?? null,
                'discount' => $request->discount ?? 0.00,
                'total_amount' => $request->total_amount,
                'amount_paid' => $request->amount_paid,
                'payment_method' => $request->payment_method,
                'user_id' => Auth::user()->id,
            ]);

            // Save sale items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id']);

                SaleItem::create([
                    'order_id' => $sale->id,
                    'product_id' => $product->id,
                    'title' => $product->name,
                    'quantity' => $item['quantity'],
                    'buying_price' => $product->buying_price,
                    'selling_price' => $product->getEffectivePrice(),
                ]);
            }

            return response()->json([
                'message' => 'Sale recorded successfully!',
                'sale_id' => $sale->id,
                'change' => number_format($request->amount_paid - $request->total_amount, 2)
            ]);
        });
    }

    public function edit(Sale $sale)
    {
        $amount_paid = $sale->amount_paid;
        $total_amount = $sale->total_amount;

        // Determine payment status
        if ($amount_paid == $total_amount) {
            $amount_paid_display = 'Paid';
        } elseif ($amount_paid < $total_amount) {
            $amount_paid_display = 'Underpaid';
        } else {
            $amount_paid_display = 'Overpaid';
        }

        return view('admin.sales.edit', compact('sale', 'amount_paid_display'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $amount_paid = $validated['amount_paid'];

        // Update sale details
        $sale->update([
            'amount_paid' => $amount_paid,
            'payment_method' => $validated['payment_method'],
        ]);

        // Update delivery details
        $sale->delivery()->update([
            'delivery_status' => $validated['delivery_status'],
        ]);

        return redirect()->back()->with('success', 'Sale details updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Sale has been deleted.');
    }

    public function receipt(Sale $sale)
    {
        return view('sales.receipt', compact('sale'));
    }

    public function checkoutCreate()
    {
        $cart = app(CartController::class)->cartItemsWithTotals();

        if (empty($cart['items'])) {
            return redirect()->route('shop-page')->withErrors(['cart' => 'Your cart is empty. Add items before proceeding to checkout.']);
        }

        $user = Auth::check() ? Auth::user() : null;

        return view('shop.checkout', compact('areas', 'cart', 'locations', 'user'));
    }

    public function checkoutStore(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:200',
            'email' => 'required|string|lowercase|email|max:255',
            'phone_number' => [
                'required',
                'string',
                'regex:/^(07|01)[0-9]{8}$/',
            ],
        ], [
            'phone_number.regex' => 'Phone number must start with 07 or 01 and have exactly 12 digits. (0746055xxx or 0116055xxx)',
        ]);

        $cart = app(CartController::class)->cartItemsWithTotals();
        $cart_items = $cart['items'];
        $cart_subtotal = $cart['subtotal'];

        if (empty($cart_items)) {
            return redirect()->route('shop-page')->withErrors(['cart' => 'Your cart is empty. Add items before proceeding to checkout.']);
        }

        $total_amount = $cart_subtotal;
        $order_number = 'O_' . Str::random(6) . '_' . date('dmy');
        $user_id = Auth::check() ? Auth::user()->id : null;

        try {
            DB::beginTransaction();

            // Create order
            $order = Sale::create([
                'order_number' => $order_number,
                'order_type' => 0,
                'discount_code' => null,
                'discount' => 0,
                'total_amount' => $total_amount,
                'payment_method' => null,
                'user_id' => $user_id,
            ]);

            // Insert order items
            foreach ($cart_items as $productId => $item) {
                SaleItem::create([
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'buying_price' => $item['buying_price'],
                    'selling_price' => $item['selling_price'],
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                ]);
            }

            // TODO: Insert payment details

            // Commit transaction (save changes)
            DB::commit();

            Session::put('order_number', $order->order_number);
            Session::forget(['cart', 'cart_count']);

            return redirect()->route('checkout.success');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Checkout failed. Please try again.']);
        }
    }


    public function checkoutSuccess()
    {
        $order_number = session('order_number');

        return view('shop.success', compact('order_number'));
    }

    public function cashierSales()
    {
        $user = Auth::id();

        $sales = Sale::where('user_id', $user)->latest()->get();
        $count_sales = $sales->count();
        $count_sales_today = Sale::where('user_id', $user)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return view('sales.cashier_sales', compact('count_sales', 'count_sales_today', 'sales'));
    }
}
