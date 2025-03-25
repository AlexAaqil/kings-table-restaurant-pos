<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $count_users = User::whereNotIn('user_level', [0, 1])
            ->where('user_status', 1)
            ->count();
        $count_admins = User::whereIn('user_level', [0, 1])->count();
        $count_cashiers = User::where('user_level', 2)->count();
        $count_all_users = User::count();

        $count_products = Product::count();
        $count_product_categories = ProductCategory::count();

        return view('dashboard.index', compact(
            'user',
            'count_users',
            'count_admins',
            'count_cashiers',
            'count_all_users',

            'count_products',
            'count_product_categories',
        ));
    }
}
