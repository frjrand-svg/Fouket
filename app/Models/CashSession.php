<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashSession extends Model
{
    protected $fillable = [
        'user_id',
        'opened_at',
        'closed_at',
        'opening_cash',
        'closing_cash',
        'total_sales',
        'total_cash',
        'total_mobile',
        'difference',
        'comment',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_cash' => 'integer',
        'closing_cash' => 'integer',
        'total_sales' => 'integer',
        'total_cash' => 'integer',
        'total_mobile' => 'integer',
        'difference' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
