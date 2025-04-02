<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sale;
use App\Models\Sales\SalesPayment;
use App\Models\Sales\Payment;
use Illuminate\Support\Facades\DB;

class SalePaymentController extends Controller
{
    public function recordPayment(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_reference' => 'nullable|string|unique:sales_payments,transaction_reference',
        ]);

        $sale = Sale::findOrFail($request->sale_id);

        // Check if it's an electronic payment
        if (in_array($request->payment_method, ['mpesa', 'card', 'bank'])) {
            $payment = Payment::where('transaction_reference', $request->transaction_reference)->first();
            
            if (!$payment) {
                return response()->json(['message' => 'Payment not found. Verify the transaction reference.'], 404);
            }

            // Link payment to sale
            DB::transaction(function () use ($sale, $payment) {
                SalesPayment::create([
                    'sale_id' => $sale->id,
                    'amount_paid' => $payment->amount_paid,
                    'payment_method' => $payment->payment_gateway,
                    'transaction_reference' => $payment->transaction_reference,
                    'payment_date' => now(),
                ]);

                // Update sale's amount_paid
                $sale->increment('amount_paid', $payment->amount_paid);
            });

            return response()->json(['message' => 'Electronic payment linked successfully.']);
        }

        // Handle manual cash payment
        SalesPayment::create([
            'sale_id' => $sale->id,
            'amount_paid' => $request->amount_paid,
            'payment_method' => 'cash',
            'payment_date' => now(),
        ]);

        $sale->increment('amount_paid', $request->amount_paid);

        return response()->json(['message' => 'Cash payment recorded successfully.']);
    }
}
