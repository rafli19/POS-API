<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReportController extends Controller
{
    private const VALID_PERIODS = ['daily', 'weekly', 'monthly', 'custom'];
    private const TOP_PRODUCTS_LIMIT = 10;

    public function salesReport(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'daily');

            if (!in_array($period, self::VALID_PERIODS)) {
                return $this->errorResponse('Invalid period. Allowed: ' . implode(', ', self::VALID_PERIODS), null, 422);
            }

            $dateRange = $this->getDateRange($period, $request);
            $query = $this->buildSalesQuery($dateRange, $request);

            $summary = $this->calculateSalesSummary($query, $period, $dateRange);
            $dailySales = $this->getDailySales($query);
            $topProducts = $this->getTopProducts($dateRange);
            $paymentMethods = $this->getPaymentMethodBreakdown($query);

            return $this->successResponse('Sales report generated successfully', [
                'period' => [
                    'type' => $period,
                    'start_date' => $dateRange['start'],
                    'end_date' => $dateRange['end']
                ],
                'summary' => $summary,
                'daily_sales' => $dailySales,
                'top_products' => $topProducts,
                'payment_methods' => $paymentMethods
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate sales report: ' . $e->getMessage());
            return $this->errorResponse('Failed to generate sales report: ' . $e->getMessage());
        }
    }

    public function stockReport(Request $request): JsonResponse
    {
        try {
            $lowStockProducts = Product::lowStock()->with('category')->orderBy('stock', 'asc')->get();
            $outOfStockProducts = Product::where('stock', 0)->where('is_active', true)->with('category')->get();
            $stockMovements = $this->getStockMovements();

            $summary = [
                'total_products' => Product::count(),
                'active_products' => Product::where('is_active', true)->count(),
                'low_stock_products' => $lowStockProducts->count(),
                'out_of_stock_products' => $outOfStockProducts->count(),
                'total_stock_value' => round(Product::where('is_active', true)->sum(DB::raw('stock * price')), 2),
                'total_low_stock_value' => round(Product::lowStock()->sum(DB::raw('stock * price')), 2)
            ];

            return $this->successResponse('Stock report generated successfully', [
                'summary' => $summary,
                'low_stock_products' => $lowStockProducts,
                'out_of_stock_products' => $outOfStockProducts,
                'stock_movements' => $stockMovements
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate stock report: ' . $e->getMessage());
            return $this->errorResponse('Failed to generate stock report');
        }
    }

    public function transactionReport(Request $request): JsonResponse
    {
        try {
            $query = Transaction::with(['user', 'details.product'])->where('status', 'completed');

            $this->applyTransactionFilters($query, $request);

            $perPage = $request->get('per_page', 20);
            $transactions = $query->orderBy('transaction_date', 'desc')->paginate($perPage);

            $summary = [
                'total_transactions' => $transactions->total(),
                'total_revenue' => $transactions->sum('total_amount'),
                'average_transaction' => $transactions->count() > 0 
                    ? round($transactions->sum('total_amount') / $transactions->count(), 2) 
                    : 0
            ];

            return $this->successResponse('Transaction report generated successfully', [
                'summary' => $summary,
                'transactions' => $transactions
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate transaction report: ' . $e->getMessage());
            return $this->errorResponse('Failed to generate transaction report');
        }
    }

    public function kasirReport(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->startOfMonth());
            $endDate = $request->get('end_date', now()->endOfMonth());

            $kasirs = User::where('role', 'kasir')
                ->where('is_active', true)
                ->withCount([
                    'transactions as transaction_count' => function($query) use ($startDate, $endDate) {
                        $query->whereBetween('transaction_date', [$startDate, $endDate])
                              ->where('status', 'completed');
                    }
                ])
                ->withSum([
                    'transactions as total_revenue' => function($query) use ($startDate, $endDate) {
                        $query->whereBetween('transaction_date', [$startDate, $endDate])
                              ->where('status', 'completed');
                    }
                ], 'total_amount')
                ->get()
                ->map(function($kasir) {
                    return [
                        'id' => $kasir->id,
                        'name' => $kasir->name,
                        'email' => $kasir->email,
                        'transaction_count' => $kasir->transaction_count,
                        'total_revenue' => round($kasir->total_revenue, 2),
                        'average_transaction' => $kasir->transaction_count > 0 
                            ? round($kasir->total_revenue / $kasir->transaction_count, 2) 
                            : 0
                    ];
                })
                ->sortByDesc('total_revenue')
                ->values();

            return $this->successResponse('Kasir report generated successfully', [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'kasirs' => $kasirs
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate kasir report: ' . $e->getMessage());
            return $this->errorResponse('Failed to generate kasir report');
        }
    }

    private function getDateRange(string $period, Request $request): array
    {
        switch ($period) {
            case 'daily':
                return [
                    'start' => today()->startOfDay(),
                    'end' => today()->endOfDay()
                ];
            case 'weekly':
                return [
                    'start' => now()->startOfWeek(),
                    'end' => now()->endOfWeek()
                ];
            case 'monthly':
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth()
                ];
            default:
                $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
                $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
                
                return [
                    'start' => Carbon::parse($startDate)->startOfDay(),
                    'end' => Carbon::parse($endDate)->endOfDay()
                ];
        }
    }

    private function buildSalesQuery(array $dateRange, Request $request)
    {
        $query = Transaction::where('status', 'completed');
        
        $start = Carbon::parse($dateRange['start']);
        $end = Carbon::parse($dateRange['end']);
        
        if ($start->isSameDay($end)) {
            // Single day - use whereDate for accuracy
            $query->whereDate('transaction_date', $start->toDateString());
        } else {
            // Multiple days - use whereBetween
            $query->whereBetween('transaction_date', [$dateRange['start'], $dateRange['end']]);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        return $query;
    }

    private function calculateSalesSummary($query, string $period, array $dateRange): array
    {
        $totalTransactions = $query->count();
        $totalRevenue = $query->sum('total_amount');

        return [
            'total_transactions' => $totalTransactions,
            'total_revenue' => round($totalRevenue, 2),
            'average_transaction_value' => $totalTransactions > 0 ? round($totalRevenue / $totalTransactions, 2) : 0,
            'transaction_growth' => $this->calculateGrowth($period, $dateRange['start'], $dateRange['end'])
        ];
    }

    private function getDailySales($query)
    {
        return $query->clone()
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    private function getTopProducts(array $dateRange)
    {
        $start = Carbon::parse($dateRange['start']);
        $end = Carbon::parse($dateRange['end']);
        
        $query = TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->where('transactions.status', 'completed');
        
        if ($start->isSameDay($end)) {
            $query->whereDate('transactions.transaction_date', $start->toDateString());
        } else {
            $query->whereBetween('transactions.transaction_date', [$dateRange['start'], $dateRange['end']]);
        }
        
        return $query->select(
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

    private function getPaymentMethodBreakdown($query)
    {
        return $query->clone()
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('payment_method')
            ->get();
    }

    private function getStockMovements()
    {
        return DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->where('transactions.transaction_date', '>=', now()->subDays(30))
            ->where('transactions.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(transaction_details.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_sold', 'desc')
            ->limit(self::TOP_PRODUCTS_LIMIT)
            ->get();
    }

    private function applyTransactionFilters($query, Request $request): void
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('transaction_date', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('customer_name')) {
            $query->where('customer_name', 'like', '%' . $request->customer_name . '%');
        }
    }

    private function calculateGrowth(string $period, $startDate, $endDate): float
    {
        try {
            switch ($period) {
                case 'daily':
                    $prevStart = Carbon::parse($startDate)->subDay()->startOfDay();
                    $prevEnd = Carbon::parse($endDate)->subDay()->endOfDay();
                    break;
                case 'weekly':
                    $prevStart = Carbon::parse($startDate)->subWeek()->startOfWeek();
                    $prevEnd = Carbon::parse($endDate)->subWeek()->endOfWeek();
                    break;
                case 'monthly':
                    $prevStart = Carbon::parse($startDate)->subMonth()->startOfMonth();
                    $prevEnd = Carbon::parse($endDate)->subMonth()->endOfMonth();
                    break;
                default:
                    return 0;
            }

            $currentRevenue = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->sum('total_amount');

            $prevRevenue = Transaction::whereBetween('transaction_date', [$prevStart, $prevEnd])
                ->where('status', 'completed')
                ->sum('total_amount');

            if ($prevRevenue == 0) {
                return $currentRevenue > 0 ? 100 : 0;
            }

            return round((($currentRevenue - $prevRevenue) / $prevRevenue) * 100, 2);

        } catch (\Exception $e) {
            return 0;
        }
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