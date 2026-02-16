<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class TransactionController extends Controller
{
    private const TOP_PRODUCTS_LIMIT = 10;

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Transaction::with(['user:id,name,email', 'details', 'paymentMethod']);
            $this->applyFilters($query, $request);

            $transactions = $query->orderBy('transaction_date', 'desc')
                ->paginate($request->get('per_page', 10));

            return $this->successResponse('Transactions retrieved successfully', $transactions);
        } catch (\Exception $e) {
            Log::error('Failed to fetch transactions: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch transactions', null, 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = $this->validateTransaction($request);
            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', $validator->errors(), 422);
            }

            DB::beginTransaction();

            try {
                $items = $this->validateAndPrepareItems($request->items);
                $totalAmount = array_sum(array_column($items, 'subtotal'));
                
                // Get payment method
                $paymentMethod = $this->getPaymentMethod($request);
                $paymentMethodId = $paymentMethod?->id;
                $paymentMethodName = $paymentMethod?->name ?? 'Cash';

                // TENTUKAN STATUS BERDASARKAN PAYMENT METHOD
                $status = 'pending'; // Default pending
                $changeAmount = 0;
                
                if ($paymentMethod?->type === 'cash') {
                    // Cash langsung completed
                    $status = 'completed';
                    $changeAmount = $request->payment_amount - $totalAmount;
                    if ($changeAmount < 0) {
                        throw new \Exception('Insufficient payment amount');
                    }
                }

                // Generate payment reference and QR code (if applicable)
                $paymentData = $this->generatePaymentReference($paymentMethod, $totalAmount);
                $paymentReference = $paymentData['reference'] ?? null;
                $qrCodeData = $paymentData['qr_code'] ?? null;

                // Create transaction
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name ?? 'Guest',
                    'total_amount' => $totalAmount,
                    'payment_method_id' => $paymentMethodId,
                    'payment_method' => $paymentMethodName,
                    'payment_amount' => $request->payment_amount ?? $totalAmount,
                    'payment_reference' => $paymentReference,
                    'change_amount' => $changeAmount,
                    'status' => $status,
                ]);

                // HANYA KURANGI STOK JIKA CASH (completed)
                if ($status === 'completed') {
                    $this->createTransactionDetailsWithStockReduction($transaction, $items);
                } else {
                    // Untuk pending, simpan detail tapi JANGAN kurangi stok dulu
                    $this->createTransactionDetailsWithoutStockReduction($transaction, $items);
                }

                DB::commit();
                
                // Prepare response with QR code if available
                $responseData = $transaction->load(['user', 'details.product', 'paymentMethod']);
                
                if ($qrCodeData) {
                    $responseData->qr_code = $qrCodeData;
                }

                return $this->successResponse('Transaction created successfully', $responseData, 201);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Failed to create transaction: ' . $e->getMessage());
            return $this->errorResponse('Failed to create transaction: ' . $e->getMessage(), null, 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $transaction = Transaction::with(['user:id,name,email', 'details.product', 'paymentMethod'])
                ->find($id);

            if (!$transaction) {
                return $this->errorResponse('Transaction not found', null, 404);
            }

            return $this->successResponse('Transaction retrieved successfully', $transaction);
        } catch (\Exception $e) {
            Log::error('Failed to fetch transaction: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch transaction', null, 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $transaction = Transaction::find($id);
            if (!$transaction) {
                return $this->errorResponse('Transaction not found', null, 404);
            }

            $validator = Validator::make($request->all(), [
                'customer_name' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:pending,completed,cancelled',
                'payment_method_id' => 'sometimes|exists:payment_methods,id',
                'payment_reference' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', $validator->errors(), 422);
            }

            $transaction->update($request->all());
            return $this->successResponse('Transaction updated successfully', $transaction->load('paymentMethod'));
        } catch (\Exception $e) {
            Log::error('Failed to update transaction: ' . $e->getMessage());
            return $this->errorResponse('Failed to update transaction', null, 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $transaction = Transaction::find($id);
            if (!$transaction) {
                return $this->errorResponse('Transaction not found', null, 404);
            }

            if ($transaction->status !== 'pending') {
                return $this->errorResponse('Cannot delete completed transaction', null, 400);
            }

            $transaction->delete();
            return $this->successResponse('Transaction deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete transaction: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete transaction', null, 500);
        }
    }

    /**
     * CONFIRM PAYMENT - KASIR VERIFIKASI
     */
    public function confirmPayment($id): JsonResponse
    {
        try {
            $transaction = Transaction::with('details.product')->find($id);
            
            if (!$transaction) {
                return $this->errorResponse('Transaction not found', null, 404);
            }

            if ($transaction->status !== 'pending') {
                return $this->errorResponse('Transaction is not pending', null, 400);
            }

            DB::beginTransaction();
            
            try {
                // Update status to completed
                $transaction->update(['status' => 'completed']);
                
                // NOW reduce stock
                foreach ($transaction->details as $detail) {
                    $product = $detail->product;
                    if ($product->stock < $detail->quantity) {
                        throw new \Exception("Insufficient stock for product: {$product->name}");
                    }
                    $product->decrement('stock', $detail->quantity);
                }
                
                DB::commit();
                
                return $this->successResponse('Payment confirmed successfully', $transaction->load(['user', 'details.product', 'paymentMethod']));
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Failed to confirm payment: ' . $e->getMessage());
            return $this->errorResponse('Failed to confirm payment: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * CANCEL PAYMENT - KASIR BATALKAN
     */
    public function cancelPayment($id): JsonResponse
    {
        try {
            $transaction = Transaction::find($id);
            
            if (!$transaction) {
                return $this->errorResponse('Transaction not found', null, 404);
            }

            if ($transaction->status !== 'pending') {
                return $this->errorResponse('Can only cancel pending transactions', null, 400);
            }

            $transaction->update(['status' => 'cancelled']);
            
            return $this->successResponse('Transaction cancelled successfully', $transaction);
        } catch (\Exception $e) {
            Log::error('Failed to cancel transaction: ' . $e->getMessage());
            return $this->errorResponse('Failed to cancel transaction', null, 500);
        }
    }

    public function statistics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

            $query = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
                ->where('status', 'completed');

            $totalTransactions = $query->count();
            $totalRevenue = $query->sum('total_amount');

            return $this->successResponse('Statistics retrieved successfully', [
                'period' => ['start_date' => $startDate, 'end_date' => $endDate],
                'summary' => [
                    'total_transactions' => $totalTransactions,
                    'total_revenue' => round($totalRevenue, 2),
                    'average_transaction_value' => $totalTransactions > 0 
                        ? round($totalRevenue / $totalTransactions, 2) 
                        : 0
                ],
                'top_products' => $this->getTopProducts($startDate, $endDate),
                'sales_by_payment_method' => $this->getSalesByPaymentMethod($query)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch statistics: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch statistics', null, 500);
        }
    }

    public function dailyReport(Request $request): JsonResponse
    {
        try {
            $date = $request->get('date', today()->toDateString());
            $transactions = Transaction::whereDate('transaction_date', $date)
                ->where('status', 'completed')
                ->with(['user:id,name', 'details.product', 'paymentMethod'])
                ->orderBy('transaction_date', 'desc')
                ->get();

            return $this->successResponse('Daily report retrieved successfully', [
                'date' => $date,
                'total_revenue' => round($transactions->sum('total_amount'), 2),
                'total_transactions' => $transactions->count(),
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch daily report: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch daily report', null, 500);
        }
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_code', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        if ($request->filled('period')) {
            match($request->period) {
                'today' => $query->today(),
                'week' => $query->thisWeek(),
                'month' => $query->thisMonth(),
                default => null
            };
        }

        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }
    }

    private function validateTransaction(Request $request)
    {
        return Validator::make($request->all(), [
            'customer_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_method_code' => 'nullable|string|exists:payment_methods,code',
            'payment_amount' => 'required_if:payment_method.type,cash|numeric|min:0',
        ]);
    }

    private function validateAndPrepareItems(array $items): array
    {
        return array_map(function($item) {
            $product = Product::findOrFail($item['product_id']);
            
            if (!$product->is_active) {
                throw new \Exception("Product '{$product->name}' is not active");
            }
            
            if ($product->stock < $item['quantity']) {
                throw new \Exception("Insufficient stock for product: {$product->name}");
            }
            
            return [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'subtotal' => $product->price * $item['quantity']
            ];
        }, $items);
    }

    // DENGAN KURANGI STOK (untuk cash)
    private function createTransactionDetailsWithStockReduction(Transaction $transaction, array $items): void
    {
        foreach ($items as $item) {
            $transaction->details()->create([
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->name,
                'product_price' => $item['product']->price,
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal']
            ]);
            
            $item['product']->decrement('stock', $item['quantity']);
        }
    }

    // TANPA KURANGI STOK (untuk pending)
    private function createTransactionDetailsWithoutStockReduction(Transaction $transaction, array $items): void
    {
        foreach ($items as $item) {
            $transaction->details()->create([
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->name,
                'product_price' => $item['product']->price,
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal']
            ]);
            
            // TIDAK kurangi stok dulu
        }
    }

    private function getTopProducts($startDate, $endDate)
    {
        return TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->where('transactions.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                \DB::raw('SUM(transaction_details.quantity) as total_sold'),
                \DB::raw('SUM(transaction_details.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_sold')
            ->limit(self::TOP_PRODUCTS_LIMIT)
            ->get();
    }

    private function getSalesByPaymentMethod($query)
    {
        return $query->clone()
            ->select(
                'payment_method_id',
                \DB::raw('COUNT(*) as count'),
                \DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('payment_method_id')
            ->get()
            ->map(function ($item) {
                $method = PaymentMethod::find($item->payment_method_id);
                return [
                    'payment_method_id' => $item->payment_method_id,
                    'payment_method_name' => $method?->name ?? 'Unknown',
                    'count' => $item->count,
                    'total' => round($item->total, 2),
                ];
            });
    }

    private function getPaymentMethod(Request $request): ?PaymentMethod
    {
        if ($request->payment_method_id) {
            return PaymentMethod::find($request->payment_method_id);
        }
        
        if ($request->payment_method_code) {
            return PaymentMethod::where('code', $request->payment_method_code)->first();
        }
        
        return null;
    }

    private function generatePaymentReference(?PaymentMethod $method, float $amount): array
    {
        if (!$method || $method->type === 'cash') {
            return ['reference' => null, 'qr_code' => null];
        }

        return match($method->code) {
            'qris' => $this->generateQRISReference($amount),
            'bca', 'mandiri', 'bri', 'bni' => [
                'reference' => $this->generateVirtualAccount($method, $amount),
                'qr_code' => null
            ],
            default => [
                'reference' => $this->generateDigitalReference($method, $amount),
                'qr_code' => null
            ]
        };
    }

    private function generateQRISReference(float $amount): array
    {
        $timestamp = now()->format('YmdHis');
        $reference = "QRIS-{$timestamp}-" . strtoupper(Str::random(8));
        
        try {
            $qrData = $reference;
            
            $options = new QROptions([
                'version'      => 5,
                'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
                'eccLevel'     => QRCode::ECC_L,
            ]);

            $qrCode = new QRCode($options);
            $qrSvg = $qrCode->render($qrData);
            
            return [
                'reference' => $reference,
                'qr_code' => $qrSvg
            ];
        } catch (\Exception $e) {
            Log::error('QR Generation Error: ' . $e->getMessage());
            
            return [
                'reference' => $reference,
                'qr_code' => null
            ];
        }
    }

    private function generateVirtualAccount(PaymentMethod $method, float $amount): string
    {
        $bankCodes = [
            'bca' => '014',
            'mandiri' => '008',
            'bri' => '002',
            'bni' => '009',
        ];
        
        $code = $bankCodes[$method->code] ?? '000';
        $timestamp = now()->format('His');
        $unique = str_pad(substr((string)(int)$amount, -4), 4, '0', STR_PAD_LEFT);
        $random = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        return "{$code}{$timestamp}{$unique}{$random}";
    }

    private function generateDigitalReference(PaymentMethod $method, float $amount): string
    {
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(6));
        
        return strtoupper($method->code) . "-{$timestamp}-{$random}";
    }

    private function successResponse(string $message, $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    private function errorResponse(string $message, $errors = null, int $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}