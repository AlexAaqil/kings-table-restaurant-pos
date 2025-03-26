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

        $count_users = User::whereNotIn('user_level', [0, 1])
            ->where('user_status', 1)
            ->count();
        $count_admins = User::whereIn('user_level', [0, 1])->count();
        $count_cashiers = User::where('user_level', 2)->count();
        $count_all_users = User::count();

        $count_products = Product::count();
        $count_product_categories = ProductCategory::count();

        $count_total_sales = Sale::count();
        $count_sales_today = Sale::whereDate('created_at', Carbon::today())->count();
        $count_sales_this_week = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();
        $count_sales_this_month = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();

        // Total sales amount calculations
        $sales_today = Sale::whereDate('created_at', Carbon::today())->sum('total_amount');
        $sales_this_week = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total_amount');
        $sales_this_month = Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('total_amount');

        // Previous period sales (for comparison)
        $sales_yesterday = Sale::whereDate('created_at', Carbon::yesterday())->sum('total_amount');
        $sales_last_week = Sale::whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->sum('total_amount');
        $sales_last_month = Sale::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
            ->sum('total_amount');

        // Calculate percentage increase or decrease
        $change_today = $this->calculatePercentageChange($sales_yesterday, $sales_today);
        $change_this_week = $this->calculatePercentageChange($sales_last_week, $sales_this_week);
        $change_this_month = $this->calculatePercentageChange($sales_last_month, $sales_this_month);

        $databaseDriver = DB::connection()->getPDO()->getAttribute(PDO::ATTR_DRIVER_NAME);
        $monthFunction = match ($databaseDriver) {
            'sqlite' => "CAST(strftime('%m', created_at) AS INTEGER)",
            default => "MONTH(created_at)",
        };

        // Sales Statistics
        $gross_sales = Sale::sum('total_amount');
        $net_sales = Sale::sum('total_amount') - Sale::sum('discount');
        $cost_of_sales = SaleItem::sum('buying_price');
        $gross_profit = $net_sales - $cost_of_sales;

        // Sales for each month
        $monthly_sales = Sale::selectRaw("$monthFunction as month, SUM(total_amount) as total_sales")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_sales', 'month');

        $sales_data = [];
        for ($month = 1; $month <= 12; $month++) {
            $sales_data[] = $monthly_sales[$month] ?? 0;
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
