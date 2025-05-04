<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Payments\KCBMpesaExpress;

class KCBMpesaExpressController extends Controller
{
    public function showForm()
    {
        return view('mpesa.pay');
    }

    public function initiatePayment(Request $request, KCBMpesaExpress $mpesa)
    {
        $validated = $request->validate([
            'phone'=> 'required|string',
            'amount' => 'required|numeric|min:1'
        ]);

        $invoice = 'ORDER-' . uniqid();

        $response = $mpesa->stkPush($validated['phone'], $validated['amount'], $invoice);

        if (isset($response['error']) && $response['error'] === true) {
            return back()->withErrors(['payment' => $response['CustomerMessage']]);
        }

        return back()->with('status', $response['CustomerMessage'] ?? 'Request sent');
    }

    public function handleCallback(Request $request)
    {
        Log::info('M-Pesa Callback: ', $request->all());

        $data = $request->input('Body.stkCallback.CallbackMetadata.Item', []);

        $parse = collect($data)->mapWithKeys(fn ($item) => [$item['Name'] => $item['value'] ?? null]);

        // Save the transaction

        return response()->json(['message' => 'Callback received']);
    }
}
