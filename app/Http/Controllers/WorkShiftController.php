<?php

namespace App\Http\Controllers;

use App\Models\WorkShift;
use App\Models\Sales\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkShiftController extends Controller
{
	public function start(Request $request)
	{
		$user = Auth::user();

		$active_shift = WorkShift::where('user_id', $user->id)->where('status', 'active')->first();
		if($active_shift) {
			return redirect()->back()->with('error', 'You already have an active shift.');
		}

		WorkShift::create([
			'user_id' => $user->id,
			'shift_start' => now(),
			'status' => 'active',
		]);

		return redirect()->back()->with('success', 'Shift has been started.');
	}

	public function end(Request $request)
	{
		
        $user = Auth::user();

        $shift = WorkShift::where('user_id', $user->id)->where('status', 'active')->first();
        if (!$shift) {
            return redirect()->back()->with('error', 'No active shift found.');
        }

        // TODO: change user_id to created_by after restructuring the sales table.
	    $total_sales = Sale::where('user_id', $user->id)
	        ->where('created_at', '>=', $shift->shift_start)
	        ->where('created_at', '<=', now())
	        ->sum('total_amount');

        // Update shift record
        $shift->update([
            'shift_end' => now(),
            'total_sales_amount' => $total_sales,
            'status' => 'closed',
        ]);

        return redirect()->back()->with('success', 'Shift has ended.');
	}

	public function calculateTotalSales()
	{
		//
	}
}
