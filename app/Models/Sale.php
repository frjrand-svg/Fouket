<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'cash_session_id',
        'reference',
        'total_amount',
        'cash_amount',
        'mobile_amount',
        'payment_method',
        'status',
        'cancellation_reason',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'cash_amount' => 'integer',
        'mobile_amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashSession()
    {
        return $this->belongsTo(CashSession::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }
}
