<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private const TRANSACTION_TARGET = 20;
    private const TOP_PRODUCTS_LIMIT = 5;
    private const RECENT_TRANSACTIONS_LIMIT = 5;

    public function summary(Request $request): JsonResponse
    {
        try {
            $date = $request->get('date', Carbon::today()->format('Y-m-d'));

            $todayRevenue = $this->getTodayRevenue($date);
            $todayTransactions = $this->getTodayTransactions($date);
            $avgTransaction = $todayTransactions > 0 ? round($todayRevenue / $todayTransactions, 2) : 0;

            $summaryCards = [
                'today_revenue' => round($todayRevenue, 2),
                'today_transactions' => $todayTransactions,
                'average_transaction' => $avgTransaction,
                'total_categories' => Category::count(),
                'total_active_products' => Product::where('is_active', true)->count()
            ];

            return $this->successResponse('Dashboard summary retrieved successfully', [
                'date' => $date,
                'summary_cards' => $summaryCards,
                'recent_transactions' => $this->getRecentTransactions(),
                'top_products' => $this->getTopProducts($date)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch dashboard summary: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch dashboard summary: ' . $e->getMessage());
        }
    }

    public function chartData(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '7days');
            $dateRange = $this->getDateRange($period);

            $salesData = $this->getSalesData($dateRange['start'], $dateRange['end']);
            $chartData = $this->formatChartData($dateRange, $salesData);

            return $this->successResponse('Chart data retrieved successfully', [
                'period' => $period,
                'sales_chart' => $chartData,
                'category_distribution' => $this->getCategoryDistribution($dateRange['start'], $dateRange['end'])
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch chart data: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch chart data');
        }
    }

    public function notifications(): JsonResponse
    {
        try {
            $notifications = [];

            $this->addLowStockNotifications($notifications);
            $this->addOutOfStockNotifications($notifications);
            $this->addTargetNotification($notifications);

            return $this->successResponse('Notifications retrieved successfully', [
                'total' => count($notifications),
                'notifications' => $notifications
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch notifications');
        }
    }

    public function quickStats(): JsonResponse
    {
        try {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $monthlyRevenue = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->where('status', 'completed')
                ->sum('total_amount');

            $monthlyTransactions = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->where('status', 'completed')
                ->count();

            $revenueGrowth = $this->calculateRevenueGrowth($monthlyRevenue);

            return $this->successResponse('Quick stats retrieved successfully', [
                'monthly_revenue' => round($monthlyRevenue, 2),
                'monthly_transactions' => $monthlyTransactions,
                'active_kasirs' => User::where('role', 'kasir')->where('is_active', true)->count(),
                'active_products' => Product::where('is_active', true)->count(),
                'revenue_growth' => $revenueGrowth,
                'period' => [
                    'current_month' => Carbon::now()->format('F Y'),
                    'last_month' => Carbon::now()->subMonth()->format('F Y')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch quick stats: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch quick stats');
        }
    }

    private function getTodayRevenue($date): float
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date, config('app.timezone', 'Asia/Jakarta'));
        
        return Transaction::whereDate('transaction_date', $date->format('Y-m-d'))
            ->where('status', 'completed')
            ->sum('total_amount');
    }

    private function getTodayTransactions($date): int
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date, config('app.timezone', 'Asia/Jakarta'));
        
        return Transaction::whereDate('transaction_date', $date->format('Y-m-d'))
            ->where('status', 'completed')
            ->count();
    }

    private function getRecentTransactions()
    {
        return Transaction::with('user')
            ->where('status', 'completed')
            ->orderBy('transaction_date', 'desc')
            ->limit(self::RECENT_TRANSACTIONS_LIMIT)
            ->get()
            ->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'transaction_code' => $transaction->transaction_code,
                    'customer_name' => $transaction->customer_name ?? 'Guest',
                    'user' => [
                        'id' => $transaction->user->id,
                        'name' => $transaction->user->name
                    ],
                    'total_amount' => round($transaction->total_amount, 2),
                    'payment_method' => $transaction->payment_method,
                    'transaction_date' => $transaction->transaction_date->toIso8601String(),
                ];
            });
    }

    private function getTopProducts($date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date, config('app.timezone', 'Asia/Jakarta'));
        
        return TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereDate('transactions.transaction_date', $date->format('Y-m-d'))
            ->where('transactions.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(transaction_details.quantity) as total_sold'),
                DB::raw('SUM(transaction_details.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_sold', 'desc')
            ->limit(self::TOP_PRODUCTS_LIMIT)
            ->get();
    }

    private function getDateRange(string $period): array
    {
        $now = Carbon::now();
        
        switch ($period) {
            case '30days':
                return [
                    'start' => $now->copy()->subDays(29)->startOfDay(),
                    'end' => $now->endOfDay(),
                    'format' => 'd M'
                ];
            case 'this_month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->endOfDay(),
                    'format' => 'd'
                ];
            default:
                return [
                    'start' => $now->copy()->subDays(6)->startOfDay(),
                    'end' => $now->endOfDay(),
                    'format' => 'd M'
                ];
        }
    }

    private function getSalesData($startDate, $endDate)
    {
        return Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');
    }

    private function formatChartData(array $dateRange, $salesData): array
    {
        $labels = [];
        $revenue = [];
        $transactions = [];

        $currentDate = $dateRange['start']->copy();
        
        while ($currentDate->lte($dateRange['end'])) {
            $dateKey = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format($dateRange['format']);
            $revenue[] = isset($salesData[$dateKey]) ? round($salesData[$dateKey]->revenue, 2) : 0;
            $transactions[] = isset($salesData[$dateKey]) ? $salesData[$dateKey]->transactions : 0;
            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions
        ];
    }

    private function getCategoryDistribution($startDate, $endDate)
    {
        return DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->where('transactions.status', 'completed')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(transaction_details.subtotal) as revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('revenue', 'desc')
            ->get();
    }

    private function addLowStockNotifications(array &$notifications): void
    {
        $lowStockProducts = Product::lowStock()->with('category')->limit(5)->get();

        foreach ($lowStockProducts as $product) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Stok Rendah',
                'message' => "Produk '{$product->name}' stok tersisa {$product->stock} (min: {$product->min_stock})",
                'timestamp' => now()->toDateTimeString()
            ];
        }
    }

    private function addOutOfStockNotifications(array &$notifications): void
    {
        $outOfStockProducts = Product::where('stock', 0)
            ->where('is_active', true)
            ->limit(5)
            ->get();

        foreach ($outOfStockProducts as $product) {
            $notifications[] = [
                'type' => 'danger',
                'title' => 'Stok Habis',
                'message' => "Produk '{$product->name}' stok habis!",
                'timestamp' => now()->toDateTimeString()
            ];
        }
    }

    private function addTargetNotification(array &$notifications): void
    {
        $todayTransactions = Transaction::whereDate('transaction_date', today())
            ->where('status', 'completed')
            ->count();

        $achievement = round(($todayTransactions / self::TRANSACTION_TARGET) * 100, 1);

        if ($achievement < 50) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Target Penjualan',
                'message' => "Pencapaian hari ini: {$todayTransactions}/" . self::TRANSACTION_TARGET . " transaksi ({$achievement}%)",
                'timestamp' => now()->toDateTimeString()
            ];
        }
    }

    private function calculateRevenueGrowth(float $monthlyRevenue): float
    {
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $lastMonthRevenue = Transaction::whereBetween('transaction_date', [$lastMonthStart, $lastMonthEnd])
            ->where('status', 'completed')
            ->sum('total_amount');

        if ($lastMonthRevenue == 0) {
            return $monthlyRevenue > 0 ? 100 : 0;
        }

        return round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2);
    }

    private function successResponse(string $message, $data = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    private function errorResponse(string $message, $errors = null, int $code = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}