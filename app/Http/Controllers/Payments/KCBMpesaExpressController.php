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
        // 1. Log the raw payload for diagnostics
        Log::channel('kcb-mpesa')->info('Raw STK Callback Payload:', $request->all());

        // 2. Pull main callback body
        $callback = $request->input('Body.stkCallback', []);

        // 3. Extract essential values
        $resultCode = $callback['ResultCode'] ?? null;
        $resultDesc = $callback['ResultDesc'] ?? null;
        $merchantRequestId = $callback['MerchantRequestID'] ?? null;
        $checkoutRequestId = $callback['CheckoutRequestID'] ?? null;

        // 4. Extract metadata fields (e.g., Amount, PhoneNumber, etc.)
        $metadata = collect($callback['CallbackMetadata']['Item'] ?? [])
            ->mapWithKeys(fn ($item) => [$item['Name'] => $item['Value'] ?? null])
            ->toArray();

        // 5. Merge and format data for saving or future DB persistence
        $parsed = [
            'ResultCode'         => $resultCode,
            'ResultDesc'         => $resultDesc,
            'MerchantRequestID'  => $merchantRequestId,
            'CheckoutRequestID'  => $checkoutRequestId,
            'Amount'             => $metadata['Amount'] ?? null,
            'MpesaReceiptNumber' => $metadata['MpesaReceiptNumber'] ?? null,
            'TransactionDate'    => $metadata['TransactionDate'] ?? null,
            'PhoneNumber'        => $metadata['PhoneNumber'] ?? null,
        ];

        // 6. Log the cleaned, structured data
        Log::channel('kcb-mpesa')->info('Parsed STK Callback:', $parsed);

        // Return 200 to notify KCB that the callback was successfully received
        return response()->json(['message' => 'Callback received'], 200);
    }
}
