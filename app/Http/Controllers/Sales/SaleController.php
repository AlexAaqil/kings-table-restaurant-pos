<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        // Dump and die to inspect received data
        Log::info('Sale Data:', $request->all()); // Log request data for debugging
        return response()->json([
            'message' => 'Sale recorded successfully!',
            'data' => $request->all(), // Return the data received for verification
        ]);
    }
}
