<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function searchPayment(Request $request)
    {
        $request->validate([
            'transaction_reference' => 'required|string',
        ]);

        $payment = Payment::where('transaction_reference', $request->transaction_reference)->first();

        if (!$payment) {
            return response()->json(['message' => 'No payment found with this reference.'], 404);
        }

        return response()->json($payment);
    }
}
