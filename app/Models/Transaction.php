<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'user_id',
        'customer_name',
        'total_amount',
        'payment_method_id',
        'payment_method',
        'payment_amount',
        'payment_reference',
        'change_amount',
        'status',
        'transaction_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    //Boot method untuk generate transaction code otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_code)) {
                $transaction->transaction_code = self::generateTransactionCode();
            }
            
            if (empty($transaction->transaction_date)) {
                $transaction->transaction_date = now();
            }
        });
    }

    //Generate unique transaction code
    public static function generateTransactionCode(): string
    {
        do {
            $code = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::where('transaction_code', $code)->exists());

        return $code;
    }

    //Relasi: Transaction belongs to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //Relasi: Transaction has many Details
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    //Relasi: Transaction belongs to PaymentMethod
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    //Filter by status
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
    
    //Filter by date range
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    //Filter transactions for today
    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', today());
    }

    //Filter transactions for this week
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('transaction_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    //Filter transactions for this month
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transaction_date', now()->month)
                     ->whereYear('transaction_date', now()->year);
    }

    //Filter by user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    //Get payment method name (fallback to old payment_method field)
    public function getPaymentMethodNameAttribute()
    {
        return $this->paymentMethod ? $this->paymentMethod->name : ($this->payment_method ?? 'Cash');
    }

    //Get payment method type
    public function getPaymentTypeAttribute()
    {
        return $this->paymentMethod ? $this->paymentMethod->type : 'cash';
    }

    //Get total quantity of all items in this transaction
    public function getTotalItemsAttribute(): int
    {
        return $this->details->sum('quantity');
    }
}