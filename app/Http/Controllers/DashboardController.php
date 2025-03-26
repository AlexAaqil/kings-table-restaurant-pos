<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDO;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // General user statistics
        $count_users = User::whereNotIn('user_level', [0, 1])
            ->where('user_status', 1)
            ->count();
        $count_admins = User::whereIn('user_level', [0, 1])->count();
        $count_cashiers = User::where('user_level', 2)->count();
        $count_all_users = User::count();

        // Product statistics
        $count_products = Product::count();
        $count_product_categories = ProductCategory::count();

        // Global sales statistics (for admins)
        $count_total_sales = Sale::count();
        $count_sales_today = Sale::whereDate('created_at', Carbon::today())->count();
        $count_sales_this_week = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $count_sales_this_month = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();

        // Total sales amount calculations (for admins)
        $sales_today = Sale::whereDate('created_at', Carbon::today())->sum('total_amount');
        $sales_this_week = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');
        $sales_this_month = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total_amount');

        // Previous period sales (for comparison)
        $sales_yesterday = Sale::whereDate('created_at', Carbon::yesterday())->sum('total_amount');
        $sales_last_week = Sale::whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->sum('total_amount');
        $sales_last_month = Sale::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->sum('total_amount');

        // Calculate percentage change
        $change_today = $this->calculatePercentageChange($sales_yesterday, $sales_today);
        $change_this_week = $this->calculatePercentageChange($sales_last_week, $sales_this_week);
        $change_this_month = $this->calculatePercentageChange($sales_last_month, $sales_this_month);

        // Cashier-specific sales filtering
        $cashier_sales_query = Sale::where('user_id', $user->id);

        // Cashier sales counts
        $count_cashier_total_sales = $cashier_sales_query->count();
        $count_cashier_sales_today = (clone $cashier_sales_query)->whereDate('created_at', Carbon::today())->count();
        $count_cashier_sales_this_week = (clone $cashier_sales_query)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();
        $count_cashier_sales_this_month = (clone $cashier_sales_query)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();

        // Cashier sales amounts
        $cashier_sales_today = (clone $cashier_sales_query)->whereDate('created_at', Carbon::today())->sum('total_amount');
        $cashier_sales_this_week = (clone $cashier_sales_query)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total_amount');
        $cashier_sales_this_month = (clone $cashier_sales_query)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('total_amount');

        // Previous period cashier sales (for comparison)
        $cashier_sales_yesterday = (clone $cashier_sales_query)->whereDate('created_at', Carbon::yesterday())->sum('total_amount');
        $cashier_sales_last_week = (clone $cashier_sales_query)
            ->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->sum('total_amount');
        $cashier_sales_last_month = (clone $cashier_sales_query)
            ->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
            ->sum('total_amount');

        // Calculate percentage change for cashier
        $cashier_change_today = $this->calculatePercentageChange($cashier_sales_yesterday, $cashier_sales_today);
        $cashier_change_this_week = $this->calculatePercentageChange($cashier_sales_last_week, $cashier_sales_this_week);
        $cashier_change_this_month = $this->calculatePercentageChange($cashier_sales_last_month, $cashier_sales_this_month);

        // Monthly sales data (for charts)
        $database_driver = DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);
        $month_function = match ($database_driver) {
            'sqlite' => "CAST(strftime('%m', created_at) AS INTEGER)",
            default => "MONTH(created_at)",
        };

        // Sales for each month (admin)
        $monthly_sales = Sale::selectRaw("$month_function as month, SUM(total_amount) as total_sales")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_sales', 'month');

        // Sales for each month (cashier)
        $cashier_monthly_sales = (clone $cashier_sales_query)
            ->selectRaw("$month_function as month, SUM(total_amount) as total_sales")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_sales', 'month');

        $sales_data = [];
        $cashier_sales_data = [];
        for ($month = 1; $month <= 12; $month++) {
            $sales_data[] = $monthly_sales[$month] ?? 0;
            $cashier_sales_data[] = $cashier_monthly_sales[$month] ?? 0;
        }

        return view('dashboard.index', compact(
            'user',
            'count_users',
            'count_admins',
            'count_cashiers',
            'count_all_users',

            'count_products',
            'count_product_categories',

            'count_total_sales',
            'count_sales_today',
            'count_sales_this_week',
            'count_sales_this_month',

            'sales_today',
            'sales_this_week',
            'sales_this_month',
            'sales_yesterday',
            'sales_last_week',

            'change_today',
            'change_this_week',
            'change_this_month',

            'sales_data',

            // Cashier-specific data
            'count_cashier_total_sales',
            'count_cashier_sales_today',
            'count_cashier_sales_this_week',
            'count_cashier_sales_this_month',

            'cashier_sales_today',
            'cashier_sales_this_week',
            'cashier_sales_this_month',
            'cashier_sales_yesterday',
            'cashier_sales_last_week',
            'cashier_sales_last_month',

            'cashier_change_today',
            'cashier_change_this_week',
            'cashier_change_this_month',

            'cashier_sales_data'
        ));
    }

    private function calculatePercentageChange($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0; // If no previous sales, assume 100% increase if current sales exist
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }
}
